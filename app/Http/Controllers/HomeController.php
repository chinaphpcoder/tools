<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller{

    /**
     * 首页
     */
    public function index() {

        $this->view_data['meta_title'] = '首页';
        return view('home.index', $this->view_data);
    }
}
