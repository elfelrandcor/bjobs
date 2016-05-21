# bjobs
Background Jobs


###### Init db
    $db = new SQLite3(__DIR__.'/src/logs/journal.db');
    $db->exec('CREATE TABLE queue_journal(id INTEGER PRIMARY KEY   AUTOINCREMENT, name TEXT NOT NULL);');
    
    $db->exec('CREATE TABLE queue_journal_log(
       id INTEGER PRIMARY KEY AUTOINCREMENT,
       date_create DATETIME NOT NULL ,
       journalId INTEGER NOT NULL,
       code INTEGER NULL,
       message TEXT NOT NULL
    );');
