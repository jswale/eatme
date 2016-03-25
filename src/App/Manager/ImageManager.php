<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Recipie;
use App\Domain\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageManager extends BaseManager
{

	public function create(Recipie $recipie, $name, UploadedFile $uploadedFile)
	{
		$bean = new Image();
		$bean->setRecipie($recipie);
		$bean->setExtension($this->getExtension($uploadedFile));
		$this->updateInternal($bean, $name);
		parent::persist($bean);

		$this->saveFile($bean, $uploadedFile);

		return $bean;
	}

	public function saveFile(Image $bean, UploadedFile $uploadedFile)
	{
		$uploadedFile->move($this->getFileFolder(), $this->getFilename($bean));
	}

	public function cleanFile(Image $bean)
	{
		$folder = $this->getFileFolder();
		$filename = $this->getFilename($bean);
		if(file_exists($folder . $filename)) {
			unlink($folder . $filename);
		}
	}

	public function update(Image $bean, $name, UploadedFile $uploadedFile = null)
	{
		$this->updateInternal($bean, $name);
		parent::persist($bean);

		if(null != $uploadedFile) {
			$this->cleanFile($bean);
			$this->saveFile($bean, $uploadedFile);

			$bean->setExtension($this->getExtension($uploadedFile));
			parent::persist($bean);
		}
	}

	protected function updateInternal(Image $bean, $name)
	{
		$bean->setName($name);
	}

	private function getFilename(Image $bean)
	{
		return $bean->getId() . "." . $bean->getExtension();
	}

	private function getFileFolder()
	{
		return $this->app['upload_dir'] . 'images/';
	}

	private function getExtension(UploadedFile $uploadedFile)
	{
		$pathinfo = pathinfo($uploadedFile->getClientOriginalName());
		return $pathinfo["extension"];
	}
}