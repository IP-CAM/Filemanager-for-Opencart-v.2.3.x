<?php
class ControllerExtensionModuleFileManager extends Controller
{

    private $data = array();
    private $version;
    private $module_path;
    private $extensions_link;
    private $language_variables;
    private $moduleModel;
    private $moduleName;
    private $call_model;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->config('letgrow/filemanager');
        $this->moduleName = $this->config->get('filemanager_name');
        $this->call_model = $this->config->get('filemanager_model');
        $this->module_path = $this->config->get('filemanager_path');
        $this->version = $this->config->get('filemanager_version');

        if (version_compare(VERSION, '2.3.0.0', '>=')) {
            $this->extensions_link = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', 'SSL');
        } else {
            $this->extensions_link = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        }

        $this->load->model($this->module_path);
        $this->moduleModel = $this->{$this->call_model};
        $this->language_variables = $this->load->language($this->module_path);

        //Loading framework models
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');

        // Common data
        $this->data['module_path'] = $this->module_path;
        $this->data['moduleName'] = $this->moduleName;
        $this->data['moduleNameSmall'] = $this->moduleName;

        // Language variables
        foreach ($this->language_variables as $code => $languageVariable) {
            $this->data[$code] = $languageVariable;
        }
    }

    public function setupView($title = null, $stylesheet = null)
    {
        // Permissions
        if ($this->user->hasPermission('access', $this->module_path)) {
            $_SESSION[$this->moduleName] = true;
            $_SESSION['OC_VERSION'] = VERSION;
            $this->data['hasAccess'] = true;
        } else {
            $this->data['hasAccess'] = false;
        }

        if ($this->user->hasPermission('modify', $this->module_path)) {
            $this->data['canModify'] = true;
        } else {
            $this->data['canModify'] = false;
        }

        // Document
        $pageTitle = $this->language->get('plugin_name') . $this->version;

        if ($title) {
            $pageTitle .= ' - ' . $title;
        }

        $this->document->setTitle($pageTitle);

        if ($stylesheet) {
            $this->document->addStyle('view/stylesheet/' . $this->moduleName . '/' . $stylesheet . '.css');
        }

        // Commons
        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['footer'] = $this->load->controller('common/footer');
        $this->data['language'] = $this->language;
    }

    public function setBreadcrumbs($last = null)
    {
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->extensions_link,
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('plugin_name') . ' ' . $this->version,
            'href' => $this->url->link($this->module_path, 'token=' . $this->session->data['token'], 'SSL'),
        );
        if ($last) {
            $this->data['breadcrumbs'][] = $last;
        }
    }

    public function index()
    {
        $this->setupView();
        $this->setBreadcrumbs();

        $baseDir = dirname(DIR_APPLICATION);
        $excludedExt = ['png', 'jpg', 'jpeg', 'PNG', 'JPEG', 'JPG'];

        $files = array_filter(
            $this->readDirectory($baseDir),
            function ($item) use ($excludedExt) {
                return !in_array(pathinfo($item, PATHINFO_EXTENSION), $excludedExt);
            }
        );

        $this->data['baseDir'] = $baseDir;
        $this->data['files'] = $files;
        $this->data['editUrl'] = $this->url->link($this->module_path . '/edit', 'token=' . $this->session->data['token'], 'SSL');

        // Load view
        $this->response->setOutput(
            $this->load->view($this->module_path . '/' . $this->moduleName . '.tpl', $this->data)
        );
    }

    public function edit()
    {
        $filename = $this->request->get['filename'];

        $this->setupView($filename);
        $this->setBreadcrumbs(
            array(
                'text' => $filename,
                'href' => $this->url->link($this->module_path, 'token=' . $this->session->data['token'], 'SSL'),
            )
        );

        $baseDir = dirname(DIR_APPLICATION);
        $fileDir = $baseDir . '/' . $filename;

        // Save file 
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->data['saveSuccess'] = true;
            $data = $this->request->post['code'];
            file_put_contents($fileDir, html_entity_decode($data));
        }

        $fileContent = file_get_contents($fileDir);
        $this->data['content'] = htmlspecialchars($fileContent);
        $this->data['filename'] = $filename;
        $this->data['fileDir'] = $fileDir;
        $this->data['editUrl'] = $this->url->link(
            $this->module_path . '/edit', 'token=' . $this->session->data['token'] . '&filename=' . $filename,
            'SSL'
        );

        $this->response->setOutput(
            $this->load->view($this->module_path . '/filepreview.tpl', $this->data)
        );
    }

    public function install()
    {
        $this->moduleModel->install();
    }

    public function uninstall()
    {
        $this->moduleModel->uninstall();
    }

    private function readDirectory($directory)
    {
        $files = [];

        foreach (scandir($directory) as $filename) {
            if ($filename[0] === '.') {
                continue;
            }

            $filePath = $directory . '/' . $filename;

            if (is_dir($filePath)) {
                foreach ($this->readDirectory($filePath) as $childFilename) {
                    $files[] = $filename . '/' . $childFilename;
                }
            } else {
                $files[] = $filename;
            }
        }

        return $files;
    }
}