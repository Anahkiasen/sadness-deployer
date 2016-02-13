<?php

namespace SadnessDeployer\Http;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SadnessDeployer\Deployer;

class DeployController extends Controller
{
    /**
     * @var Deployer
     */
    protected $deployer;

    /**
     * @param Deployer $deployer
     */
    public function __construct(Deployer $deployer)
    {
        $this->deployer = $deployer;
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request)
    {
        return $this->runTask($request, 'deploy');
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function setup(Request $request)
    {
        return $this->runTask($request, 'setup');
    }

    /**
     * @param Request $request
     * @param string  $task
     *
     * @return View
     */
    protected function runTask(Request $request, $task)
    {
        $this->deployer->setPretend($request->get('pretend'));

        return view('sadness-deployer::console', [
            'output' => $this->deployer->$task(),
        ]);
    }
}
