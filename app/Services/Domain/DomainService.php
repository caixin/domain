<?php

namespace App\Services\Domain;

use App\Repositories\Domain\DomainRepository;
use App\Repositories\Domain\DomainGroupRepository;
use App\Repositories\Domain\NodeRepository;
use App\Repositories\Domain\DetectRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DomainService
{
    protected $domainRepository;
    protected $domainGroupRepository;
    protected $nodeRepository;
    protected $detectRepository;

    public function __construct(
        DomainRepository $domainRepository,
        DomainGroupRepository $domainGroupRepository,
        NodeRepository $nodeRepository,
        DetectRepository $detectRepository
    ) {
        $this->domainRepository = $domainRepository;
        $this->domainGroupRepository = $domainGroupRepository;
        $this->nodeRepository = $nodeRepository;
        $this->detectRepository = $detectRepository;
    }

    public function list($input)
    {
        $search_params = param_process($input, ['id', 'asc']);
        $order         = $search_params['order'];
        $search        = $search_params['search'];
        $params_uri    = $search_params['params_uri'];

        $node_all = $this->nodeRepository->count();
        $result = $this->domainRepository->search($search)
            ->order($order)->paginate(session('per_page'))
            ->result()->appends($input);
        foreach ($result as $key => $row) {
            $count = 0;
            foreach ($row->detects as $arr) {
                $arr['status'] == 0 && $count++;
            }
            $row['health'] = round($count / $node_all * 100)."%($count/$node_all)";
            $result[$key] = $row;
        }

        $search['group_id'] = $search['group_id'] ?? 0;
        return [
            'result'     => $result,
            'search'     => $search,
            'order'      => $order,
            'params_uri' => $params_uri,
            'group'      => $this->domainGroupRepository->getGroupList(),
        ];
    }

    public function create($input)
    {
        $row = $this->domainRepository->getEntity();
        $row['group_id'] = $input['group_id'] ?? 0;
        $row['deadline'] = date('Y-m-d', time()+86400*365);
        if ($input['id'] > 0) {
            $row = $this->domainRepository->row($input['id']);
        }

        return [
            'row'   => $row,
            'group' => $this->domainGroupRepository->getGroupList(),
        ];
    }

    public function store($row)
    {
        $row = array_map('strval', $row);
        $id = $this->domainRepository->create($row);

        $node = $this->nodeRepository->result();
        $insert = [];
        foreach ($node as $arr) {
            $insert[] = [
                'node_id'    => $arr['id'],
                'domain_id'  => $id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => session('username') ?: '',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('username') ?: '',
            ];
        }
        if ($insert != []) {
            $this->detectRepository->insert_batch($insert);
        }
    }

    public function show($id)
    {
        $row = $this->domainRepository->find($id);

        return [
            'row'   => $row,
            'group' => $this->domainGroupRepository->getGroupList(),
        ];
    }

    public function update($row, $id)
    {
        $row = array_map('strval', $row);

        $this->domainRepository->update($row, $id);
    }

    public function save($row, $id)
    {
        $this->domainRepository->save($row, $id);
    }

    public function destroy($id)
    {
        $this->domainRepository->delete($id);
        $this->detectRepository->search(['domain_id'=>$id])->delete();
    }

    public function export($input)
    {
        $search_params = param_process($input, ['id', 'asc']);
        $order         = $search_params['order'];
        $search        = $search_params['search'];

        $result = $this->domainRepository
            ->search($search)
            ->order($order)
            ->result();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueByColumnAndRow(1, 1, 'SSL凭证(0:否 1:是)');
        $sheet->setCellValueByColumnAndRow(2, 1, '网域');
        $sheet->setCellValueByColumnAndRow(3, 1, '群组ID');
        $sheet->setCellValueByColumnAndRow(4, 1, '网域到期日');
        $sheet->setCellValueByColumnAndRow(5, 1, '购买地点');
        $sheet->setCellValueByColumnAndRow(6, 1, '备注');

        $r = 1;
        foreach ($result as $row) {
            $r++;
            $sheet->setCellValueByColumnAndRow(1, $r, $row['ssl']);
            $sheet->setCellValueByColumnAndRow(2, $r, $row['domain']);
            $sheet->setCellValueByColumnAndRow(3, $r, $row['group_id']);
            $sheet->setCellValueByColumnAndRow(4, $r, $row['deadline']);
            $sheet->setCellValueByColumnAndRow(5, $r, $row['supplier']);
            $sheet->setCellValueByColumnAndRow(6, $r, $row['remark']);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="domain.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function import($request)
    {
        $file = $request->file("file");
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $total = $sheet->getHighestRow();

        $exist = '';
        for ($r = 2; $r <= $total; $r++) {
            if (trim($sheet->getCellByColumnAndRow(1, $r)->getValue()) == '') {
                continue;
            }

            $domain = (string)$sheet->getCellByColumnAndRow(2, $r)->getValue();
            if ($this->domainRepository->search(['domain'=>$domain])->count() > 0) {
                $exist .= "$domain 已存在<br>";
                continue;
            }

            $this->store([
                'domain'   => $domain,
                'ssl'      => (string)$sheet->getCellByColumnAndRow(1, $r)->getValue(),
                'group_id' => (string)$sheet->getCellByColumnAndRow(3, $r)->getValue(),
                'deadline' => (string)$sheet->getCellByColumnAndRow(4, $r)->getValue(),
                'supplier' => (string)$sheet->getCellByColumnAndRow(5, $r)->getValue(),
                'remark'   => (string)$sheet->getCellByColumnAndRow(6, $r)->getValue(),
            ]);
        }
        
        if ($exist == '') {
            echo json_encode([
                'status'  => 1,
                'message' => '汇入成功',
                'exist'   => '',
            ], 320);
        } else {
            echo json_encode([
                'status'  => 0,
                'message' => '部分汇入成功',
                'exist'   => $exist,
            ], 320);
        }
    }

    public function healthList()
    {
        $node_all = $this->nodeRepository->count();
        $group = $this->domainGroupRepository->getGroupList();
        $list = [];
        foreach ($group as $id => $name) {
            $result = $this->domainRepository
                ->search(['group_id' => $id])
                ->order(['id', 'asc'])
                ->result();
            $domains = [];
            foreach ($result as $row) {
                $count = 0;
                foreach ($row->detects as $arr) {
                    $arr['status'] == 0 && $count++;
                }
                $percent = round($count / $node_all * 100);
                $domains[] = [
                    'domain_id' => $row['id'],
                    'domain'    => $row['domain'],
                    'percent'   => $percent,
                    'health'    => $percent."%($count/$node_all)",
                ];
            }

            if ($domains != []) {
                $list[] = [
                    'name'   => $name,
                    'domains' => $domains,
                ];
            }
        }
        return $list;
    }
}
