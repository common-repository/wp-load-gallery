<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class mainGoogleDrive
 * This class that holds most of the admin functionality for Google Drive
 */
class mainGoogleDrive
{

    /**
     * Params
     *
     * @var $param
     */
    protected $params;

    /**
     * Breadcrumb
     *
     * @var string
     */
    public $breadcrumb = '';

    /**
     * Files fields
     *
     * @var string
     */
    protected $filesfields = 'nextPageToken,items(thumbnailLink,alternateLink,id,description,labels(hidden,restricted,trashed),embedLink,etag,downloadUrl,iconLink,exportLinks,mimeType,modifiedDate,fileExtension,webContentLink,fileSize,userPermission,imageMediaMetadata(width,height),kind,permissions(kind,name,role,type,value,withLink),parents(id,isRoot,kind),title,openWithLinks),kind';

    /**
     * mainGoogleDrive constructor.
     *
     * @param string $type Google photo or google drive
     */
    public function __construct($type = 'google-drive')
    {
        require_once WPLG_PLUGIN_DIR . '/includes/Google/autoload.php';
        $this->loadParams($type);
    }

    /**
     * Is Shared Drive
     *
     * @param array $configs Configs
     *
     * @return boolean
     */
    public function isTeamDrives($configs)
    {
        if (!empty($configs['drive_type']) && $configs['drive_type'] === 'team_drive') {
            return true;
        }

        return false;
    }

    /**
     * Get cloud configs
     *
     * @param string $type Google photo or google drive
     *
     * @return mixed
     */
    public function getParams($type = 'google-drive')
    {
        $default = array(
            'ClientId' => '',
            'ClientSecret' => '',
            'drive_type' => 'my_drive',
            'connected' => 0
        );

        if ($type === 'google-drive') {
            return get_option('wplg_google_drive_configs', $default);
        } else {
            return get_option('wplg_google_photo_configs', $default);
        }
    }

    /**
     * Load google drive params
     *
     * @param string $type Google photo or google drive
     *
     * @return void
     */
    protected function loadParams($type = 'google-drive')
    {
        $params = $this->getParams($type);
        $this->params = new \stdClass();
        $this->params->google_client_id = isset($params['ClientId']) ? $params['ClientId'] : '';
        $this->params->google_client_secret = isset($params['ClientSecret']) ? $params['ClientSecret'] : '';
        $this->params->google_credentials = isset($params['googleCredentials']) ? $params['googleCredentials'] : '';
    }

    /**
     * Save google drive params
     *
     * @return void
     */
    protected function saveParams()
    {
        $params = $this->getParams();
        $params['ClientId'] = $this->params->google_client_id;
        $params['ClientSecret'] = $this->params->google_client_secret;
        $params['googleCredentials'] = $this->params->google_credentials;
        $this->saveCloudConfigs($params);
    }

    /**
     * Save cloud configs
     *
     * @param array  $data Data config
     * @param string $type Google photo or google drive
     *
     * @return boolean
     */
    public function saveCloudConfigs($data, $type = 'google-drive')
    {
        if ($type === 'google-drive') {
            $result = update_option('wplg_google_drive_configs', $data);
        } else {
            $result = update_option('wplg_google_photo_configs', $data);
        }

        return $result;
    }


    /**
     * Get author url
     *
     * @return string
     */
    public function getAuthorisationUrl()
    {
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $uri = admin_url('admin.php?page=load_gallery_cloud&action=google_drive_connect');
        $client->setRedirectUri($uri);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setState('');
        $client->setScopes(array(
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/drive.appdata',
            'https://www.googleapis.com/auth/drive.apps.readonly',
            'https://www.googleapis.com/auth/drive.file'
        ));

        $tmpUrl = parse_url($client->createAuthUrl());
        $query = explode('&', $tmpUrl['query']);
        $url = $tmpUrl['scheme'] . '://' . $tmpUrl['host'];
        if (isset($tmpUrl['port'])) {
            $url .= $tmpUrl['port'] . $tmpUrl['path'] . '?' . implode('&', $query);
        } else {
            $url .= $tmpUrl['path'] . '?' . implode('&', $query);
        }

        return $url;
    }

    /**
     * Redirect url
     *
     * @param string $location URL
     *
     * @return void
     */
    public function redirect($location)
    {
        if (!headers_sent()) {
            header('Location: ' . $location, true, 303);
        } else {
            // phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
            echo "<script>document.location.href='" . str_replace("'", '&apos;', $location) . "';</script>\n";
        }
    }

    /**
     * Access google drive
     *
     * @param string $type Google photo or google drive
     *
     * @return void
     */
    public function ggAuthenticated($type = 'google-drive')
    {
        $credentials = $this->authenticate($type);
        if ($type === 'google-drive') {
            $this->storeCredentials($credentials);
            $data                     = $this->getParams($type);
            $data['connected']  = 1;
            update_option('wplg_google_drive_configs', $data);
        } else {
            $data = get_option('wplg_google_photo_configs', true);
            $data['googleCredentials']  = $credentials;
            $data['connected']  = 1;
            update_option('wplg_google_photo_configs', $data);
        }

        $this->redirect(admin_url('admin.php?page=load_gallery_cloud'));
    }

    /**
     * Access google drive app
     *
     * @param string $type Google photo or google drive
     *
     * @return string
     */
    public function authenticate($type = 'google-drive')
    {
        $code = (isset($_GET['code'])) ? $_GET['code'] : null;
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        if ($type === 'google-drive') {
            $url = admin_url('admin.php?page=load_gallery_cloud&action=google_drive_connect');
        } else {
            $url = admin_url('admin.php?page=load_gallery_cloud&action=google_photo_connect');
        }

        $client->setRedirectUri($url);
        return $client->authenticate($code);
    }

    /**
     * Logout google drive app
     *
     * @return void
     */
    public function disConnect()
    {
        $params = $this->getParams();
        $params['googleCredentials'] = '';
        $params['connected'] = 0;
        $this->saveCloudConfigs($params);
        $this->redirect(admin_url('admin.php?page=load_gallery_cloud'));
    }

    /**
     * Set credentials
     *
     * @param string $credentials Credentials
     *
     * @return void
     */
    public function storeCredentials($credentials)
    {
        $this->params->google_credentials = $credentials;
        $this->saveParams();
    }

    /**
     * Get credentials
     *
     * @return mixed
     */
    public function getCredentials()
    {
        return $this->params->google_credentials;
    }

    /**
     * Check author
     *
     * @return array
     */
    public function checkAuth()
    {
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);

        try {
            $client->setAccessToken($this->params->google_credentials);
            $service = new Google_Service_Drive($client);
            $service->files->listFiles(array());
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
        return array('success' => true);
    }

    /**
     * Get Google Client
     *
     * @param array $config Google client config
     *
     * @return Google_Client
     */
    public function getClient($configs)
    {
        $client = new Google_Client();
        $client->setClientId($configs['ClientId']);
        $client->setClientSecret($configs['ClientSecret']);
        $client->setAccessToken($configs['googleCredentials']);

        if ($client->isAccessTokenExpired()) {
            $googleCredentials = json_decode($configs['googleCredentials']);
            $client->refreshToken($googleCredentials->refresh_token);
            $tokenDetails = $client->getAccessToken();
            $client->setAccessToken($tokenDetails);
            $configs['googleCredentials'] = $tokenDetails;
            update_option('wplg_google_drive_configs', $configs);
        }
        return $client;
    }

    /**
     * Get link
     *
     * @param string $drive_id Cloud file ID
     *
     * @return boolean|string
     */
    public function getLink($drive_id)
    {
        $config = get_option('wplg_google_drive_configs');
        $client = $this->getClient($config);
        $service = new Google_Service_Drive($client);
        try {
            $userPermission = new Google_Service_Drive_Permission(array(
                'type' => 'anyone',
                'role' => 'reader',
            ));
            $service->permissions->create($drive_id, $userPermission, array('fields' => 'id', 'supportsAllDrives' => $this->isTeamDrives($config)));
            $link = 'https://drive.google.com/uc?id=' . $drive_id;
        } catch (Exception $e) {
            $link = false;
        }

        return $link;
    }
}
