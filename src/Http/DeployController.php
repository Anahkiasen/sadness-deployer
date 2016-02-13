<?php
namespace SadnessDeployer\Http;

use Illuminate\Contracts\View\View;
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
     * @Get("deploy", as="deploy.deploy")
     * @return View
     */
    public function index()
    {
        return view('console', [
            'output' => $this->deployer->deploy(),
        ]);
    }
}
