
--
-- Dumping data for table `organisation`
--

LOCK TABLES `organisation` WRITE;
/*!40000 ALTER TABLE `organisation` DISABLE KEYS */;
INSERT INTO `organisation` VALUES (1,'AgileTech','',0,0,0),(2,'Joe Software Company','A sample client for our screencast',0,0,0),(3,'Eantrix','',0,0,0),(4,'Imants','DarkSide666 co',0,0,0),(5,'Bozims','Bozims Co',0,0,0),(6,'Agile55.com Demo','',0,0,0);
/*!40000 ALTER TABLE `organisation` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
  (1,'Root Admin','admin','0dc2f385e18e42962b3bcd93501185b3',NULL,0,1,1,1,0,NULL,'8bfe167008aa9671fdec70f88cc901d6',NULL,1,1,0,1,0,'6462b01c4935ce04d13c8f8242942dd1',NULL,0,'8fa04c4c5f0800380ef971d5bf6d168d','2014-08-21 10:13:16'),
  (2,'Manager','man','202cb962ac59075b964b07152d234b70',NULL,0,0,1,NULL,0,NULL,NULL,NULL,0,1,1,1,0,NULL,NULL,0,NULL,'2014-08-19 16:58:57'),
  (3,'Developer','dev','202cb962ac59075b964b07152d234b70',NULL,0,0,0,NULL,1,NULL,NULL,NULL,0,1,1,1,0,NULL,NULL,0,NULL,'2014-08-19 16:58:57'),
  (4,'Vadym Manager','vadym_m','2118c37083b5b7eeb4ad3a148dba89ad',NULL,0,0,1,NULL,0,NULL,'87a637545e059d0a4ec4f7f9f643073f',NULL,0,1,1,1,1,NULL,NULL,0,NULL,'2014-08-19 16:58:57'),
  (5,'Vadym Radvansky','vadym','bf56a2c2906dad738716de48dc167b8e',NULL,0,1,1,0,1,NULL,'ef351a94bda040079e780af02aac1575',NULL,0,1,0,1,0,'7b78935ae6153b38c2f5fc6df060430b',544,0,'0642b0793415f19288c67222edc24fc2','2015-01-25 21:29:18'),
  (6,'Oleksii Ostapets','oleksii','7276cd5a6d3c82a1371c1af4e461846a',NULL,0,0,1,0,1,NULL,'494db4e3c38a658cdb3cce8d9ddbe7db',NULL,0,1,0,1,0,'92b66a8daacdbab95dd8ec69462460fa',550,0,'ef03ecbffdbfbd46dd5111fd72e412f4','2014-11-16 20:37:03'),
  (7,'Romans','romans','e90ed2aa5fc95d7e39cd000d6e04146c',NULL,0,1,1,1,1,NULL,'0799672d5a5807fbfd52ae51da028c6b',NULL,1,1,0,1,0,'53849afea58ec7212185d54602e215a2',NULL,0,NULL,'2014-08-19 16:58:57'),
  (8,'Aleksejs Cizevskis123','alex','77417ff9519a1ce1e7aff751b1edd915',NULL,0,1,1,1,0,0,'d1bc5c52e0bda5a3fcd0a5a65c64a869',NULL,0,1,0,1,0,'a9bf20dc56075bd72d95748409cf0391',NULL,0,'ffc0172b8513d40dc6f8afbde8feaafa','2014-08-19 16:58:57'),
  (9,'Kostya','konstantin','3c709b10a5d47ba33d85337dd9110917',NULL,0,0,1,0,1,NULL,'8e97eaa8558808b2571602f0e5facec3',NULL,0,1,0,1,0,'22c3ed0698a75c3a17337424e4878e67',546,0,'ba99eb7674c246ebeba57cfb71e518af','2014-11-18 18:28:20')
;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;




--
-- Dumping data for table `right`
--

LOCK TABLES `right` WRITE;
/*!40000 ALTER TABLE `right` DISABLE KEYS */;
INSERT INTO `right` VALUES
  (1,1,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (2,2,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (3,3,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (4,4,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (5,5,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (6,6,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (7,7,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (8,8,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'),
  (9,9,'can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user')
;
/*!40000 ALTER TABLE `right` ENABLE KEYS */;
UNLOCK TABLES;