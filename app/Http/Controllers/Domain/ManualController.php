<?php

namespace App\Http\Controllers\Domain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Domain\NodeRepository;
use App\Repositories\Domain\DomainGroupRepository;
use App\Repositories\Domain\DomainRepository;
use Validator;
use Exception;

class ManualController extends Controller
{
    protected $nodeRepository;
    protected $domainGroupRepository;
    protected $domainRepository;

    public function __construct(
        NodeRepository $nodeRepository,
        DomainGroupRepository $domainGroupRepository,
        DomainRepository $domainRepository
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->domainGroupRepository = $domainGroupRepository;
        $this->domainRepository = $domainRepository;
    }

    public function index(Request $request)
    {
        return view('manual.index', [
            'node'  => $this->nodeRepository->getList(),
            'group' => $this->domainGroupRepository->getGroupList(),
        ]);
    }

    public function action(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'node_id'  => 'required',
                'group_id' => 'required',
                'page'     => 'required',
            ]);
            if ($validator->fails()) {
                $message = implode("\n", $validator->errors()->all());
                throw new Exception($message, 422);
            }
            //節點
            $node = $this->nodeRepository->row($request->node_id);
            //網域群組
            $group = $this->domainGroupRepository->row($request->group_id);
            //驗證器集合
            $verifications = [];
            $group['mode'] & 1 && $verifications['hash']['match'] = $group['value1'];
            $group['mode'] & 2 && $verifications['css_selector']['selector'] = $group['value2'];
            $group['mode'] & 4 && $verifications['content']['find'][] = $group['value3'];
            $group['mode'] & 8 && $verifications['content']['find'][] = $group['value4'];

            $result = $this->domainRepository
                ->search(['group_id'=>$request->group_id])
                ->order(['id','asc'])
                ->paginate(1)
                ->result();

            $output['last_page'] = $result->lastPage();
            $output['data'] = [];
            foreach ($result as $row) {
                $url = ($row['ssl'] ? 'https':'http')."://$row[domain]$group[path]";

                $data_string = json_encode([
                    'url'           => $url,
                    'verifications' => $verifications,
                ]);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://$node[server_ip]/check/domain/");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: '.strlen($data_string)
                ]);
                $output['data'][] = json_decode(curl_exec($ch));
                curl_close($ch);
            }

            return response()->json($output);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
