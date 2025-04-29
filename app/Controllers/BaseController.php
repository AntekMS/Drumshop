<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['url', 'form', 'text'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();

        // Warenkorb-Session initialisieren
        if (!$this->session->has('session_id')) {
            $this->session->set('session_id', session_id());
        }
    }

    /**
     * Lädt mehrere Views und kombiniert sie zu einer Ausgabe
     *
     * @param string $content View für den Hauptinhalt
     * @param array $data Daten für die Views
     * @param string $header View für den Header (optional)
     * @param string $footer View für den Footer (optional)
     * @return string
     */
    protected function renderView($content, $data = [], $header = 'templates/header', $footer = 'templates/footer')
    {
        return view($header, $data)
            . view($content, $data)
            . view($footer, $data);
    }

    /**
     * Lädt mehrere Admin-Views und kombiniert sie
     *
     * @param string $content View für den Hauptinhalt
     * @param array $data Daten für die Views
     * @return string
     */
    protected function renderAdminView($content, $data = [])
    {
        return view('admin/templates/header', $data)
            . view($content, $data)
            . view('admin/templates/footer', $data);
    }

    /**
     * Sendet eine JSON-Antwort zurück
     *
     * @param mixed $data Daten für die Antwort
     * @param int $status HTTP-Statuscode
     * @return ResponseInterface
     */
    protected function jsonResponse($data, $status = 200)
    {
        return $this->response->setStatusCode($status)
            ->setJSON($data);
    }
}