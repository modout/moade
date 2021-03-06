<?php

/* 
 * BOOT
 * -- CHECK IF STATUS FILE EXISTS
 * -- IF TRUE
 * ---  CHECK IF STATUS SCHEMA FILE EXISTS
 * ---  IF TRUE
 * ----     GET CURRENT STATUS FROM STATUS SCHEMA
 * ---- ELSE 
 * ---      GET CURRENT STATUS FROM STATUS FILE
 * ---  IF CURRENT STATUS IS SCHEMA_EXISTS
 * ----     SEND MESSAGE TO DATA TO ASK FOR MESSAGES IN MESSAGE SCHEDULE IN PRIORITY ORDER
 * ----     CHANGE STATUS TO READ_SCHEDULE IN STATUS SCHEMA
 * ---  ELSE IF CURRENT STATUS IS CREATE_SCHEMA
 * ----     GOTO CREATE_SCHEMA
 * ---  ELSE IF CURRENT STATUS IS CHECK_SCHEMA_STATUS
 * ----     CHECK IF PROCESSOR FILE HAS MESSAGE
 * ----     IF TRUE
 * -----        CHECK IF MESSAGE IS SCHEMA_CREATED
 * -----        IF TRUE
 *                  CREATE APP (EXPLODE BY PROCESSING) PROCESSOR AND STATUS SCHEMA FILES
 * ------           CHANGE STATUS TO SCHEMA_EXISTS
 * ---  ELSE IF CURRENT STATUS IS CHECK_DATA_STATUS
 * ----     CHANGE STATUS TO CREATE_SCHEMA
 * ---  ELSE IF CURRENT STATUS IS CHECK_MESSAGE_STATUS
 * ----     CHANGE STATUS TO SCHEDULE_MESSAGES
 * ---  ELSE IF CURRENT STATUS IS READ SCHEDULE
 * ----     GOTO READ_SCHEDULE
 * ---  ELSE
 * ----     CHANGE STATUS TO READ_SCHEDULE
 * --  
 * -- 
 * -- ELSE 
 * ---  CREATE STATUS FILE
 * ---  CREATE QUEUE FILE
 * ---  CREATE INBOX FILE
 * ---  CHANGE STATUS TO INIT IN STATUS FILE
 * 
 * INIT
 * -- CHECK IF SERVICE SCHEDULED IN CRON BY CHECKING CRON FILE
 * -- IF CRON FILE DOES NOT EXIST
 * ---  CREATE CRON FILE
 * --
 * -- CHANGE STATUS TO CREATE_SCHEMA IN STATUS FILE
 * -- GOTO CREATE SCHEMA  
 * 
 * CREATE SCHEMA
 * -- CHECK IF DATA SERVICE IS ACTIVE (RUNNING)
 * -- IF TRUE
 * ---  SEND MESSAGE TO DATA TO CREATE MESSAGE QUEUE, INBOX AND STATUS SCHEMA IF NOT EXIST - WRITE INTO DATA SCHEMA QUEUE
 * ---  CHANGE STATUS TO CHECK_SCHEMA_STATUS IN STATUS FILE
 * -- ELSE
 * ---  SEND MESSAGE TO SYSTEM TO START DATA SERVICE - WRITE INTO SYSTEM QUEUE FILE
 * ---  CHANGE STATUS TO CHECK_DATA_STATUS IN STATUS FILE
 * 
 * READ SCHEDULE
 * -- CHECK IF DATA SERVICE IS ACTIVE (RUNNING)
 * -- IF TRUE
 * ---  SEND MESSAGE TO DATA TO PROCESS MESSAGES BY ADDING MESSAGE ID OF MESSAGE SCHEDULE IN SCHEDULE PRIORITY TO PROCESS TABLE WITH STATUS WAITING
 * -- ELSE
 * ---  SEND MESSAGE TO SYSTEM TO START DATA SERVICE - WRITE INTO SYSTEM FILE QUEUE
 * ---  CHANGE STATUS TO CHECK_MESSAGE_STATUS IN STATUS FILE  
 */



