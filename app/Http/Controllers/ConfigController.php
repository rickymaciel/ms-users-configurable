<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\User;
use App\UserProfile;
use App\Role;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    /**
     * @var /storage/json/config.json
     */
    protected $path;
    protected $config;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->path = storage_path() . "/json/config.json";
    	if (file_exists($this->path)) {
    		$this->config = json_decode(file_get_contents($this->path), true);
    	}
        
    }

    public function getConfig()
    {
    	try {
    		if (!is_null($this->config)) {
    			return response()->json($this->success($this->config));
    		}

    		return response()->json($this->error('No se encuentra el archivo de configuracion.'));
    		
    	} catch (Exception $e) {
    		\Log::info('getConfig', ['path', $this->path, 'config', $this->config]);
    		return response()->json($this->error($e->getMessage()));
    	}
    }
}