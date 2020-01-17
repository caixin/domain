<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Domain\DomainRepository;
use App\Repositories\Domain\DetectRepository;
use App\Repositories\Domain\JumpLogRepository;
use Validator;
use Exception;

class JumpController extends Controller
{
    protected $domainRepository;
    protected $detectRepository;
    protected $jumpLogRepository;

    public function __construct(
        DomainRepository $domainRepository,
        DetectRepository $detectRepository,
        JumpLogRepository $jumpLogRepository
    ) {
        $this->domainRepository = $domainRepository;
        $this->detectRepository = $detectRepository;
        $this->jumpLogRepository = $jumpLogRepository;
    }

    /**
     * @OA\Post(
     *   path="/jump_urls",
     *   summary="取得轉跳網址",
     *   tags={"Jump"},
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="token",
     *                   description="Token",
     *                   type="string",
     *                   example="",
     *               ),
     *               @OA\Property(
     *                   property="domain",
     *                   description="網域名稱",
     *                   type="string",
     *                   example="aaa123.com",
     *               ),
     *               @OA\Property(
     *                   property="amount",
     *                   description="取得網址數量",
     *                   type="integer",
     *                   example="10",
     *               ),
     *               required={"token","domain","amount"}
     *           )
     *       )
     *   ),
     *   @OA\Response(response="200", description="Success")
     * )
     */
    public function urls(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token'  => 'required',
                'domain' => 'required',
            ]);
            if ($validator->fails()) {
                $message = implode("\n", $validator->errors()->all());
                throw new Exception($message, 422);
            }
            $amount = $request->amount ?? 10;
            $self = $this->domainRepository->search([
                'domain' => $request->domain
            ])->result_one();
            if ($self === null) {
                throw new Exception('查无此网域群组', 422);
            }
            $urls = [];
            if ($self->group->target_id > 0) {
                $result = $this->detectRepository->select(['domain_id','COUNT(domain_id) count'])
                    ->search([
                        'group_id' => $self->group->target_id,
                        'status'   => 0,
                    ])->group('domain_id')
                    ->order(['count','desc'])
                    ->limit([0,$amount])
                    ->result();
                foreach ($result as $row) {
                    $http = $row['ssl'] ? 'https':'http';
                    $domain = $row->domain->domain;
                    $urls[] = [
                        'domain_id' => $row['domain_id'],
                        'url'       => "{$http}://$domain".$self->group->target->path,
                        'identity'  => "{$http}://$domain".$self->group->target->verify_path,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'urls'    => $urls,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * @OA\Post(
     *   path="/jump_logs",
     *   summary="跳轉錯誤LOG",
     *   tags={"Jump"},
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="token",
     *                   description="Token",
     *                   type="string",
     *                   example="",
     *               ),
     *               @OA\Property(
     *                   property="ip",
     *                   description="客戶端IP",
     *                   type="string",
     *                   example="192.168.1.100",
     *               ),
     *               @OA\Property(
     *                   property="url",
     *                   description="當前網址",
     *                   type="string",
     *                   example="aaa123.com",
     *               ),
     *               @OA\Property(
     *                   property="domain_id",
     *                   description="網域ID",
     *                   type="integer",
     *                   example="1",
     *               ),
     *               @OA\Property(
     *                   property="status",
     *                   description="狀態",
     *                   type="integer",
     *                   example="1",
     *               ),
     *               required={"token","ip","url","domain_id","status"}
     *           )
     *       )
     *   ),
     *   @OA\Response(response="200", description="Success")
     * )
     */
    public function logs(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token'     => 'required',
                'ip'        => 'required',
                'url'       => 'required',
                'domain_id' => 'required',
                'status'    => 'required',
            ]);
            if ($validator->fails()) {
                $message = implode("\n", $validator->errors()->all());
                throw new Exception($message, 422);
            }

            $domain = $this->domainRepository->row($request->domain_id);
            if ($domain === null) {
                throw new Exception('查无此网域ID', 422);
            }

            $this->jumpLogRepository->create([
                'ip'        => $request->ip,
                'url'       => $request->url,
                'domain_id' => $request->domain_id,
                'status'    => $request->status,
            ]);

            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
