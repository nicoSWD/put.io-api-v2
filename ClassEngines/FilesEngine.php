<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * This class manages anything related to 'Files'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#files
 *
**/

class FilesEngine extends ClassEngine
{
    
    /**
     * Returns an array of files.
     *
     * @param integer $parentID  OPTIONAL - Only returns files of $parentID if supplied
     * @return array
     *
    **/
    public function listall($parentID = 0)
    {
        return $this->get('files/list', array('parent_id' => $parentID));
    }
    
    
    /**
     * Returns an array of files matching the given search query.
     *
     * @param string  $query   Search query
     * @param integer $page    OPTIONAL - Page number
     * @return array
     *
    **/
    public function search($query, $page = 1)
    {
        return $this->get('files/search/' . rawurlencode(trim($query)) . '/page/' . $page);
    }
    
    
    /**
     * Uploads a local file to your account.
     *
     * @param string $file        Path to local file.
     * @param integer $parentID   OPTIONAL - ID of upload folder.
     * @return array
     *
    **/
    public function upload($file, $parentID = 0)
    {
        return $this->uploadFile('files/upload', array('parent_id', $parentID, 'file' => '@' . realpath($file)));
    }
    
    
    /**
     * Creates a new folder
     *
     * @param string $name        Name of the new folder.
     * @param integer $parentID   OPTIONAL - ID of the parent folder.
     * @return array
     *
    **/
    public function makeDir($name, $parentID = 0)
    {
        return $this->post('files/create-folder', array('name' => $name, 'parent_id' => $parentID));
    }
    
    
    /**
     * Returns an array of information about given file.
     *
     * @param integer $fileID   ID of the file.
     * @return array
     *
    **/
    public function info($fileID)
    {
        return $this->get('files/' . $fileID);
    }
    
    
    /**
     * Deletes files from your account.
     *
     * @param array $fileIDs   IDs of files you want to delete.
     * @return boolean
     *
    **/
    public function delete($fileIDs)
    {
        return $this->post('files/delete', array('file_ids' => is_array($fileIDs) ? implode(',', $fileIDs) : $fileIDs), true);
    }
    
    
    /**
     * Renames a file.
     *
     * @param integer $fileID  ID of the file you want to rename.
     * @param string  $name    New name of the file.
     * @return boolean
     *
    **/
    public function rename($fileID, $name)
    {
        return $this->post('files/rename', array('file_id' => $fileID, 'name' => $name), true);
    }
    
    
    /**
     * Moves one of more files to a new directory.
     *
     * @param array $fileIDs      IDs of files you want to move.
     * @param integer $parentID   ID of the folder you want to move the files to.
     * @return boolean
     *
    **/
    public function move(array $fileIDs, $parentID)
    {
        return $this->post('files/move', array('file_ids' => (is_array($fileIDs) ? implode(',', $fileIDs) : $fileIDs), 'parent_id' => $parentID), true);
    }
    
    
    /**
     * Converts a remote file to MP4 (whenever possible).
     *
     * @param integer $fileID   ID of the file you want to convert.
     * @return boolean
     *
    **/
    public function convertToMP4($fileID)
    {
        return $this->post('files/' . $fileID . '/mp4', array(), true);
    }
    
    
    /**
     * Returns information about the conversation process of a specific file.
     *
     * @param integer $fileID  ID of the file you want to get the status of.
     * @return array
     *
    **/
    public function getMP4Status($fileID)
    {
        return $this->get('files/' . $fileID . '/mp4');
    }
    
    
    /**
     * Downloads a remote file to the local server.
     *
     * @param integer $fileID  ID of the file you want to download.
     * @param string $saveAS   Local path you want to save the file to.
     * @return boolean
     *
    **/
    public function download($fileID, $saveAs)
    {
        return $this->downloadFile('files/' . $fileID . '/download', $saveAs);
    }
    
    
    /**
     * Downloads the MP4 version of a file if available.
     *
     * @param integer $fileID   ID of the file you want to download.
     * @param string  $saveAS   Local path you want to save the file to.
     * @return boolean
     *
    **/
    public function downloadMP4($fileID, $saveAS)
    {
        return $this->downloadFile('files/' . $fileID . '/mp4/download', $saveAs);
    }
}

?>