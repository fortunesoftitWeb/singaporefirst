CREATE TABLE IF NOT EXISTS `#__krakenimage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createDate` DATETIME NOT NULL , 
  `filePath` varchar(255) NOT NULL COMMENT 'file path of the file' ,
  `originalFileSize` BIGINT NULL DEFAULT 0 COMMENT 'Size in bytes of the original source file' ,
  `reducedFileSize` BIGINT NULL DEFAULT 0 COMMENT 'Size in bytes of the new reduced file' ,
  `lastReduceDate` DATETIME NOT NULL COMMENT 'date and time of the last reduce operation on the file' ,
  `lastReduceStatus` int(11) NULL DEFAULT 0 COMMENT 'status code from last reduce operation on the file' ,
   PRIMARY KEY  (`id`),
   KEY (`filePath`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
 
 