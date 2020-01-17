<?php
/**
 * @OA\Info(
 *   title="網域API",
 *   version="1.0"
 * )
 */

/**
 * @OA\Server(
 *   url="{schema}://domaintestapi.f1good.com",
 *   description="測試機",
 *   @OA\ServerVariable(
 *       serverVariable="schema",
 *       enum={"https", "http"},
 *       default="http"
 *   )
 * )
 *
 * @OA\Server(
 *   url="{schema}://api.domain",
 *   description="本機開發",
 *   @OA\ServerVariable(
 *       serverVariable="schema",
 *       enum={"https", "http"},
 *       default="http"
 *   )
 * )
 */
