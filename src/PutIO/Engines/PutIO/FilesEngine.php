<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * This class manages anything related to 'Files'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#files
 */
namespace PutIO\Engines\PutIO;

use PutIO\Helpers\PutIO\PutIOHelper;

/**
 * Class FilesEngine
 * @package PutIO\Engines\PutIO
 */
final class FilesEngine extends PutIOHelper
{
    /**
     * Returns an array of files. False on error.
     *
     * @param integer $parentID  Only returns files of $parentID if supplied
     * @return mixed
     */
    public function listall($parentID = 0)
    {
        $params = [
            'parent_id' => $parentID
        ];

        return $this->get('files/list', $params, \false, 'files');
    }

    /**
     * Returns an array of files matching the given search query.
     *
     * @param string  $query   Search query
     * @param integer $page    Page number
     * @return array
     */
    public function search($query, $page = 1)
    {
        return $this->get(sprintf(
            'files/search/%s/page/%d',
            rawurlencode(trim($query)),
            $page
        ));
    }

    /**
     * Uploads a local file to your account.
     *
     * NOTE 1: The response differs based on the uploaded file. For regular
     * files, the array key containing the info is 'file', but for torrents it's
     * 'transfer'.
     *
     * @see https://api.put.io/v2/docs/#files-upload
     *
     * NOTE 2: Files need to be read into the memory when using NATIVE
     * functions. Keep that in mind when uploading large files or running
     * multiple instances.
     *
     * @param string  $file        Path to local file.
     * @param integer $parentID    ID of upload folder.
     * @return mixed
     * @throws \Exception
     */
    public function upload($file, $parentID = 0)
    {
        if (!$file = realpath($file)) {
            throw new \Exception('File not found');
        }

        return $this->uploadFile('files/upload', [
            'parent_id' => $parentID,
            'file'      => "@{$file}"
        ]);
    }

    /**
     * Creates a new folder. Returns folder info on success, false on error.
     *
     * @param string  $name        Name of the new folder.
     * @param integer $parentID    ID of the parent folder.
     * @return mixed
     */
    public function makeDir($name, $parentID = 0)
    {
        $data = [
            'name'      => $name,
            'parent_id' => $parentID
        ];

        return $this->post('files/create-folder', $data, \false, 'file');
    }

    /**
     * Returns an array of information about given file. False on error.
     *
     * @param integer $fileID   ID of the file.
     * @return mixed
     */
    public function info($fileID)
    {
        return $this->get("files/{$fileID}", [], \false, 'file');
    }

    /**
     * Deletes files from your account.
     *
     * @param mixed $fileIDs  IDs of files you want to delete. Array or integer.
     * @return boolean
     */
    public function delete($fileIDs)
    {
        if (is_array($fileIDs)) {
            $fileIDs = implode(',', $fileIDs);
        }

        $data = [
            'file_ids' => $fileIDs
        ];

        return $this->post('files/delete', $data, \true);
    }

    /**
     * Renames a file.
     *
     * @param integer $fileID  ID of the file you want to rename.
     * @param string  $name    New name of the file.
     * @return boolean
     */
    public function rename($fileID, $name)
    {
        $data = [
            'file_id' => $fileID,
            'name'    => $name
        ];

        return $this->post('files/rename', $data, \true);
    }

    /**
     * Moves one of more files to a new directory.
     *
     * @param mixed   $fileIDs  IDs of files you want to move. Array or integer.
     * @param integer $parentID ID of the folder you want to move the files to.
     * @return boolean
     */
    public function move($fileIDs, $parentID)
    {
        if (is_array($fileIDs)) {
            $fileIDs = implode(',', $fileIDs);
        }

        $data = [
            'file_ids'  => $fileIDs,
            'parent_id' => $parentID
        ];

        return $this->post('files/move', $data, \true);
    }

    /**
     * Converts a remote file to MP4 (whenever possible).
     *
     * @param integer $fileID   ID of the file you want to convert.
     * @return boolean
     */
    public function convertToMP4($fileID)
    {
        return $this->post("files/{$fileID}/mp4", [], \true);
    }

    /**
     * Returns information about the conversation process of a specific file.
     *
     * @param integer $fileID    ID of the file you want to get the status of.
     * @return array
     */
    public function getMP4Status($fileID)
    {
        return $this->get("files/{$fileID}/mp4", [], \false, 'mp4');
    }

    /**
     * Downloads a remote file to the local server. Second parameter '$saveAs'
     * is optional, but very recommended. If it's left empty, it'll query for
     * the original file name by sending an additional HTTP request.
     *
     * @param integer  $fileID ID of the file you want to download.
     * @param string   $saveAs Local path you want to save the file to.
     * @param boolean  $isMP4  Tells whether or not to download the MP4 version
     *                              of a file.
     * @return boolean
     */
    public function download($fileID, $saveAs = '', $isMP4 = \false)
    {
        if ($saveAs === '') {
            if (!$info = $this->info($fileID)) {
                return \false;
            }

            $saveAs = $info['name'];
        }

        $mp4 = $isMP4 ? 'mp4/' : '';
        return $this->downloadFile("files/{$fileID}/{$mp4}download", $saveAs);
    }

    /**
     * Downloads the MP4 version of a file if available.
     * Alias of FilesEngine::download($fileID, $saveAS, true)
     *
     * @see self::download()
     *
     * @param integer $fileID   ID of the file you want to download.
     * @param string  $saveAs   Local path you want to save the file to.
     * @return boolean
     */
    public function downloadMP4($fileID, $saveAs = '')
    {
        return $this->download($fileID, $saveAs, \true);
    }

    /**
     * Returns the download URL of a given file ID.
     * ATTENTION: The URL includes your OAuth Token!
     * Don't share this URL with strangers.
     *
     * @param integer $fileID   ID of the file you want to download.
     * @param boolean  $isMP4   Tells whether or not to download the MP4 version
     *                              of a file.
     * @return string
     */
    public function getDownloadURL($fileID, $isMP4 = \false)
    {
        return sprintf(
            'https://api.put.io/v2/files/%d/%sdownload?oauth_token=%s',
            $fileID,
            $isMP4 ? 'mp4/' : '',
            $this->putio->getOAuthToken()
        );
    }
}
