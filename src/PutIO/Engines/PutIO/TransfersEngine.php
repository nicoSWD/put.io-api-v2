<?php

/**
 * Copyright (C) 2012-2015 Nicolas Oelgart
 *
 * @author Nicolas Oelgart
 * @license MIT http://opensource.org/licenses/MIT
 *
 * This class manages anything related to 'Transfers'.
 * For precise return values see put.io's documentation here:
 *
 * https://api.put.io/v2/docs/#transfers
 */
namespace PutIO\Engines\PutIO;

use PutIO\Helpers\PutIO\PutIOHelper;

/**
 * Class TransfersEngine
 * @package PutIO\Engines\PutIO
 */
final class TransfersEngine extends PutIOHelper
{
    /**
     * Returns an array of active transfers.
     *
     * @return array
     */
    public function listall() : array
    {
        return $this->get('transfers/list', [], \false, 'transfers');
    }
    
    /**
     * Adds a new transfer to the queue.
     *
     * @param string $url          URL of the file/torrent
     * @param int    $parentID     ID of the target folder. 0 = root
     * @param bool   $extract      Extract file when download complete
     * @param string $callbackUrl  put.io will POST the metadata of the file to
     *                                  the given URL when file is ready.
     * @return array
     */
    public function add(
        string $url,
        int $parentID = 0,
        bool $extract = \false,
        string $callbackUrl = ''
    ) : array {
        $data = [
            'url'            => $url,
            'save_parent_id' => $parentID,
            'extract'        => ($extract ? 'True' : 'False'),
            'callback_url'   => $callbackUrl
        ];

        return $this->post('transfers/add', $data, \false);
    }
    
    /**
     * Returns an array containing information about the transfer.
     *
     * @param integer $transferID   ID of the transfer
     * @return array
     */
    public function info(int $transferID) : array
    {
        return $this->get("transfers/{$transferID}", [], \false);
    }
    
    /**
     * Retries a given transfer.
     *
     * @param int $transferID   Transfer IDs you want to retry.
     * @return boolean
     */
    public function retry(int $transferID) : bool
    {
        $data = [
            'id' => $transferID
        ];

        return $this->post('transfers/retry', $data, \true);
    }

    /**
     * Cancels given transfers.
     *
     * @param int|array $transferIDs   Transfer IDs you want to cancel.
     * @return boolean
     */
    public function cancel($transferIDs) : bool
    {
        if (is_array($transferIDs)) {
            $transferIDs = implode(',', $transferIDs);
        }

        $data = [
            'transfer_ids' => $transferIDs
        ];

        return $this->post('transfers/cancel', $data, \true);
    }

    /**
     * Cleans completed transfers from the list.
     *
     * @return boolean
     */
    public function clean() : bool
    {
        return $this->post('transfers/clean', [], \true);
    }
}
