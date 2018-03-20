<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\DataObject\Consent;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Data\Consent;
use Pimcore\Model\Element\Note;

class Service
{

    /**
     * Inserts note for consent based to give object.
     *
     * @param AbstractObject $object
     * @param string $fieldname
     * @param string $consentContent
     * @param array $metaData
     * @return Note
     */
    public function insertConsentNote(AbstractObject $object, string $fieldname, string $consentContent, array $metaData = []) : Note {
        $note = new Note();
        $note->setCid($object->getId());
        $note->setCtype('object');
        $note->setType("consent-given");
        $note->setTitle("Consent given for field " . $fieldname);
        $note->setDate(time());

        $note->addData('consent-content', 'text', $consentContent);
        foreach($metaData as $key => $data) {
            $note->addData($key, 'text', $data);
        }
        $note->save();

        return $note;
    }

    /**
     * Inserts note for revoke based to give object.
     *
     * @param AbstractObject $object
     * @param string $fieldname
     * @return Note
     */
    public function insertRevokeNote(AbstractObject $object, string $fieldname) : Note {
        $note = new Note();
        $note->setCid($object->getId());
        $note->setCtype('object');
        $note->setType("consent-revoked");
        $note->setTitle("Consent revoked for field " . $fieldname);
        $note->setDate(time());
        $note->save();

        return $note;
    }

    /**
     * Give consent to given fieldname - sets field value and adds note
     *
     * @param AbstractObject $object
     * @param string $fieldname
     * @param string $consentContent
     * @param array $metaData
     * @return Note
     * @throws \Exception
     */
    public function giveConsent(AbstractObject $object, string $fieldname, string $consentContent, array $metaData = []): Note {
        $setter = "set" . ucfirst($fieldname);
        if(!method_exists($object, $setter)) {
            throw new \Exception("Method $setter does not exist in given object.");
        }

        $note = $this->insertConsentNote($object, $fieldname, $consentContent, $metaData);

        $object->$setter(new Consent(true, $note->getId()));
        $object->save();

        return $note;
    }


    /**
     * Revoke consent to given fieldname - sets field value and adds note
     *
     * @param AbstractObject $object
     * @param string $fieldname
     * @return Note
     * @throws \Exception
     */
    public function revokeConsent(AbstractObject $object, string $fieldname): Note {
        $setter = "set" . ucfirst($fieldname);
        if(!method_exists($object, $setter)) {
            throw new \Exception("Method $setter does not exist in given object.");
        }

        $note = $this->insertRevokeNote($object, $fieldname);

        $object->$setter(new Consent(false, $note->getId()));
        $object->save();

        return $note;
    }

}