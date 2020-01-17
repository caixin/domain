<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Domain\DomainGroupRepository;
use App\Repositories\Domain\DomainRepository;
use App\Repositories\Domain\DetectRepository;
use App\Repositories\Domain\DetectLogRepository;
use Validator;
use Exception;

class DetectController extends Controller
{
    protected $domainGroupRepository;
    protected $domainRepository;
    protected $detectRepository;
    protected $detectLogRepository;

    public function __construct(
        DomainGroupRepository $domainGroupRepository,
        DomainRepository $domainRepository,
        DetectRepository $detectRepository,
        DetectLogRepository $detectLogRepository
    ) {
        $this->domainGroupRepository = $domainGroupRepository;
        $this->domainRepository = $domainRepository;
        $this->detectRepository = $detectRepository;
        $this->detectLogRepository = $detectLogRepository;
    }

    /**
     * @OA\Post(
     *   path="/token",
     *   summary="取得token",
     *   tags={"Detect"},
     *   @OA\Response(response="200", description="Success")
     * )
     */
    public function token(Request $request)
    {
        // 加密
        $str = time();
        $key = config('app.encrypt_key');
        $encode = openssl_encrypt($str, 'AES-256-ECB', $key, 0);

        return response()->json([
            'success' => true,
            'token'   => $encode,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/domain",
     *   summary="檢測網域清單",
     *   tags={"Detect"},
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
     *               required={"token"}
     *           )
     *       )
     *   ),
     *   @OA\Response(response="200", description="Success")
     * )
     */
    public function domain(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
            ]);
            if ($validator->fails()) {
                $message = implode("\n", $validator->errors()->all());
                throw new Exception($message, 422);
            }

            $result = $this->domainGroupRepository->search(['status'=>1])->result();
            $data = [];
            foreach ($result as $row) {
                $urls = [];
                foreach ($row->domains as $arr) {
                    $urls[] = [
                        'domain_id' => $arr['id'],
                        'url'       => ($arr['ssl'] ? 'https':'http')."://$arr[domain]$row[path]",
                    ];
                }
                //驗證器集合
                $verifications = [];
                $row['mode'] & 1 && $verifications['match'] = $row['value1'];
                $row['mode'] & 2 && $verifications['selector'] = $row['value2'];
                $row['mode'] & 4 && $verifications['find'][] = $row['value3'];
                $row['mode'] & 8 && $verifications['find'][] = $row['value4'];

                $data[] = [
                    'group_id'      => $row['id'],
                    'name'          => $row['name'],
                    'verifications' => $verifications,
                    'urls'          => $urls,
                ];
            }

            return response()->json([
                'success' => true,
                'data'    => $data,
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
     *   path="/detect_result",
     *   summary="網域檢測結果",
     *   tags={"Detect"},
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
     *                   property="node_id",
     *                   description="節點ID",
     *                   type="integer",
     *                   example="1",
     *               ),
     *               @OA\Property(
     *                   property="domain_id",
     *                   description="網域ID",
     *                   type="integer",
     *                   example="1",
     *               ),
     *               @OA\Property(
     *                   property="status",
     *                   description="檢測結果 0:正常 1:失效 2:綁架",
     *                   type="integer",
     *                   example="0",
     *               ),
     *               required={"token","node_id","domain_id","status"}
     *           )
     *       )
     *   ),
     *   @OA\Response(response="200", description="Success")
     * )
     */
    public function detectResult(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token'     => 'required',
                'node_id'   => 'required',
                'domain_id' => 'required',
                'status'    => 'required',
            ]);
            if ($validator->fails()) {
                $message = implode("\n", $validator->errors()->all());
                throw new Exception($message, 422);
            }

            $detect = $this->detectRepository->search([
                'node_id'   => $request->node_id,
                'domain_id' => $request->domain_id,
            ])->result_one();

            if ($detect['lock_time'] <= '2000-01-01') {
                $detect->status = $request->status;
                $detect->updated_at = date('Y-m-d H:i:s');
                $detect->updated_by = 'node';
                $detect->save();
            }

            $this->detectLogRepository->insert([
                'node_id'   => $request->node_id,
                'domain_id' => $request->domain_id,
                'status'    => $request->status,
            ]);

            return response()->json([
                'success' => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
