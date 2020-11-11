<?php

namespace EasyImageSlider;

use Concrete\Core\Controller\Controller as RouteController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\File;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Http\Response;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class Tools extends RouteController
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct()
    {
        parent::__construct();
        $this->app = Application::getFacadeApplication();
    }

    /**
     * Endpoint of the /easyimageslider/tools/savefield route.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function save()
    {
        $file = $this->getFile();
        if ($file === null) {
            return $this->buildFailureReponse(t('File Not Found.'));
        }
        $fp = new Checker($file);
        if (!$fp->canEditFileProperties()) {
            return $this->buildFailureReponse(t('Accedd Denied.'));
        }
        $fv = $this->file->getVersionToModify();
        $name = (string) $this->request->request->get('name', $this->request->query->get('name'));
        $value = (string) $this->request->request->get('value', $this->request->query->get('value'));
        switch ($name) {
            case 'fvTitle':
                $fv->updateTitle($value);
                break;
            case 'fvDescription':
                $fv->updateDescription($value);
                break;
            case 'fvTags':
                $fv->updateTags($value);
                break;
            default:
                $fv->setAttribute($name, $value);
                break;
        }

        $sr = new EditResponse();
        $sr->setFile($this->file);
        $sr->setMessage(t('File updated successfully.'));
        $sr->setAdditionalDataAttribute('value', $value);

        return $this->buildSuccessResponse($sr);
    }

    /**
     * Endpoint of the /easyimageslider/tools/getfilesetimages route.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFileSetImages()
    {
        $fsID = $this->request->request->get('fsID', $this->request->query->get('fsID'));
        $valn = $this->app->make('helper/validation/numbers');
        $fs = $valn->integer($fsID) && $fsID > 0 ? FileSet::getByID($fsID) : null;
        if ($fs === null) {
            return $this->buildFailureReponse(t('Unable to find the requested file set.'));
        }
        $detailList = array();
        foreach ($fs->getFiles() as $file) {
            $detailList[] = $this->buildFileDetails($file);
        }

        return $this->buildSuccessResponse($detailList);
    }

    /**
     * Endpoint of the /easyimageslider/tools/getfiledetails route.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFileDetails()
    {
        $file = $this->getFile();
        if ($file === null) {
            return $this->buildFailureReponse(t('File Not Found.'));
        }
        $details = $this->buildFileDetails($file);

        return $this->buildSuccessResponse($details);
    }

    /**
     * @param \Concrete\Core\File\File $file
     *
     * @return \EasyImageSlider\FileDetails
     */
    public function buildFileDetails($file)
    {
        $o = new FileDetails();
        $o->urlInline = $this->buildFileThumbnailUrl($file);
        $o->title = (string) $file->getTitle();
        $o->description = (string) $file->getDescription();
        $o->fID = (int) $file->getFileID();
        $o->type = (string) $file->getVersionToModify()->getGenericTypeText();
        $w = (int) $file->getAttribute('image_thumbnail_width');
        $o->image_thumbnail_width = $w > 0 ? $w : null;
        $o->image_link = (string) $file->getAttribute('image_link');
        $o->image_link_text = (string) $file->getAttribute('image_link_text');
        $o->image_bg_color = (string) $file->getAttribute('image_bg_color');

        return $o;
    }

    /**
     * @return \Concrete\Core\File\File|null
     */
    protected function getFile()
    {
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));
        $valn = $this->app->make('helper/validation/numbers');
        if (!$valn->integer($fID) || $fID < 1) {
            return null;
        }

        return File::getByID($fID);
    }

    /**
     * @param mixed $data
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function buildSuccessResponse($data)
    {
        return new JsonResponse($data);
    }

    /**
     * @param string $message
     *
     * @return \Concrete\Core\Http\Response
     */
    protected function buildFailureReponse($message)
    {
        return new Response(
            (string) $message,
            Response::HTTP_BAD_REQUEST,
            array(
                'Content-Type' => 'text/plain; charset=' . APP_CHARSET,
            )
        );
    }

    /**
     * @param \Concrete\Core\File\File $file
     *
     * @return string
     */
    protected function buildFileThumbnailUrl($file)
    {
        $type = Type::getByHandle('file_manager_detail');
        if ($type === null) {
            return '';
        }

        return (string) $file->getThumbnailURL($type->getBaseVersion());
    }
}
