<?php

namespace Modules\Recruit\Http\Controllers\Front;

use App\Http\Controllers\Controller;

class FrontBaseController extends Controller
{
    /**
     * @var array
     */
    public $data = [];

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->global = global_setting();

    }
}
