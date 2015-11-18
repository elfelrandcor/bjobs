<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Journal;


use JuriyPanasevich\Logger\Journal\AbstractJournal;
use JuriyPanasevich\Logger\Journal\AbstractJournalLog;

class QueueFileJournal extends AbstractJournal {

    protected $entity;
    protected $storage;

    public function setEntity($entity) {
        $this->entity = $entity;
    }

    public function getEntity() {
        return $this->entity;
    }

    /** @return AbstractJournalLog */
    public function getJournalLog() {
        $log = new QueueFileJournalLog();
        $log->setJournal($this);
        return $log;
    }

    public function find() {
        return $this;
    }

    public function save() {
        return true;
    }

    /**
     * @return mixed
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * @param mixed $storage
     */
    public function setStorage($storage) {
        $this->storage = $storage;
    }
}