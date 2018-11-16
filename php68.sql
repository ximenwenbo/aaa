-- MySQL dump 10.13  Distrib 5.5.53, for Win32 (AMD64)
--
-- Host: localhost    Database: php68
-- ------------------------------------------------------
-- Server version	5.5.53

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `sp_goods`
--

DROP TABLE IF EXISTS `sp_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_name` varchar(128) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `goods_number` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `goods_weight` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品重量',
  `goods_introduce` text COMMENT '商品详情介绍',
  `goods_logo` char(128) NOT NULL DEFAULT '' COMMENT '商品图片',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '记录删除时间，假(逻辑)删除',
  PRIMARY KEY (`goods_id`),
  UNIQUE KEY `goods_name` (`goods_name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sp_goods`
--

LOCK TABLES `sp_goods` WRITE;
/*!40000 ALTER TABLE `sp_goods` DISABLE KEYS */;
INSERT INTO `sp_goods` VALUES (1,'huawei_nova_1s',1500.00,100,0,NULL,'',1530848883,1530848883,NULL),(2,'huawei_nova_2s',1500.00,100,0,NULL,'',1530849010,1530849010,NULL),(3,'huawei_mate_10',1000.00,0,0,NULL,'',1530859292,1530859292,NULL),(4,'huawei_mate_11',1001.00,0,0,NULL,'',1530859292,1530859292,NULL),(5,'huawei_mate_12',1002.00,0,0,NULL,'',1530859292,1530859292,NULL),(7,'huawei_mate_20',1000.00,0,0,NULL,'',1530859738,1530859738,NULL),(8,'huawei_mate_21',1001.00,0,0,NULL,'',1530859738,1530859738,NULL),(9,'xiaomi_plus',3000.00,10,20,NULL,'',1530859738,1530859738,NULL),(10,'huawei_mate_30',2400.00,0,0,NULL,'',1530859880,1530867821,1530867821),(11,'huawei_mate_31',2400.00,0,0,NULL,'',1530859880,1530867821,1530867821),(12,'huawei_mate_32',2400.00,0,0,NULL,'',1530859880,1530867821,1530867821),(13,'小米Note1',2400.00,61,121,NULL,'',NULL,NULL,NULL),(14,'小米Note2',2400.00,62,122,NULL,'',NULL,NULL,NULL),(15,'小米Note3',2400.00,63,123,NULL,'',NULL,NULL,NULL),(16,'红米5A',2400.00,81,101,NULL,'',NULL,NULL,NULL),(17,'红米5B',2400.00,82,102,NULL,'',NULL,NULL,NULL),(18,'红米5C',2400.00,83,103,NULL,'',NULL,NULL,NULL),(21,'fff',222.00,255,444,'是否示范点士大夫大师傅是','',1532077148,1532077148,NULL),(22,'e\'e\'e\'e',222.00,255,444,'是否示范点士大夫大师傅是','',1532077163,1532077163,NULL),(23,'qwer',3333.00,22,33,'<p>sdfdsfsfds</p>','',1532077248,1532156699,1532156699),(24,'aaaa',11.00,22,33,'sfsdfsdfsdf','',1532077357,1532142183,1532142183),(25,'ssss',11.00,22,33,'sdfsfdsfs','',1532078058,1532141599,1532141599),(26,'tttxxx',22.00,255,44,'<p>思路看待历史</p>','',1532078087,1532141429,1532141429),(27,'oppo_5s',234.00,102,33,'<p>很好</p>','',1532078303,1532141588,1532141588),(28,'vvwww',11.00,111,333,'<p>sfdd<span style=\"color: rgb(255, 0, 0);\">sfdsfd</span>sfds</p>','',1532079191,1532157272,NULL),(29,'ddioioio',98333.69,22,60,'<p>ksdsdslds</p>','',1532162119,1532162119,NULL),(30,'dfdsfdsf',23.12,45,103,'','',1532163172,1532163425,NULL),(31,'oppo6s',2000.03,25,130,'<p>oppo is very good</p>','./uploads/goodstmp/20180724/0c4c6b67dd2b03a3e106334e83373ac8.jpg',1532425808,1532425808,NULL);
/*!40000 ALTER TABLE `sp_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sp_manager`
--

DROP TABLE IF EXISTS `sp_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_manager` (
  `mg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `mg_name` varchar(32) NOT NULL COMMENT '名称',
  `mg_pwd` char(32) NOT NULL COMMENT '密码',
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '归属角色id',
  `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`mg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=503 DEFAULT CHARSET=utf8 COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sp_manager`
--

LOCK TABLES `sp_manager` WRITE;
/*!40000 ALTER TABLE `sp_manager` DISABLE KEYS */;
INSERT INTO `sp_manager` VALUES (500,'admin','e10adc3949ba59abbe56e057f20f883e',0,NULL,NULL,NULL),(501,'jack','e10adc3949ba59abbe56e057f20f883e',30,NULL,NULL,NULL),(502,'linken','e10adc3949ba59abbe56e057f20f883e',31,NULL,NULL,NULL);
/*!40000 ALTER TABLE `sp_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sp_permission`
--

DROP TABLE IF EXISTS `sp_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_permission` (
  `ps_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ps_name` varchar(20) NOT NULL COMMENT '权限名称',
  `ps_pid` smallint(6) unsigned NOT NULL COMMENT '父id',
  `ps_c` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `ps_a` varchar(32) NOT NULL DEFAULT '' COMMENT '操作方法',
  `ps_level` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '权限等级',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '记录删除时间，假(逻辑)删除',
  PRIMARY KEY (`ps_id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8 COMMENT='权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sp_permission`
--

LOCK TABLES `sp_permission` WRITE;
/*!40000 ALTER TABLE `sp_permission` DISABLE KEYS */;
INSERT INTO `sp_permission` VALUES (101,'商品管理',0,'','','0',1532313010,1532313010,NULL),(102,'订单管理',0,'','','0',1532313010,1532313010,NULL),(103,'权限管理',0,'','','0',1532313010,1532313010,NULL),(104,'商品列表',101,'Goods','index','1',1532313010,1532313010,NULL),(105,'品牌列表',101,'Brand','index','1',1532313010,1532313010,NULL),(106,'商品分类',101,'Category','index','1',1532313010,1532313010,NULL),(107,'订单列表',102,'Order','index','1',1532313010,1532313010,NULL),(108,'订单打印',102,'Order','dayin','1',1532313010,1532313010,NULL),(109,'添加订单',102,'Order','tianjia','1',1532313010,1532313010,NULL),(110,'管理员列表',103,'Manager','index','1',1532313010,1532313010,NULL),(111,'角色列表',103,'Role','index','1',1532313010,1532313010,NULL),(112,'权限列表',103,'Permission','index','1',1532313010,1532313010,NULL),(113,'修改',107,'Order','xiugai','2',NULL,NULL,NULL),(114,'删除商品',104,'Goods','shanchu','2',1532397517,1532397517,NULL),(115,'文章管理',0,'','','0',1532397549,1532397549,NULL),(116,'删除订单',107,'Order','shanchu','2',1532398572,1532398572,NULL);
/*!40000 ALTER TABLE `sp_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sp_role`
--

DROP TABLE IF EXISTS `sp_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_role` (
  `role_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_name` varchar(20) NOT NULL COMMENT '角色名称',
  `role_ps_ids` varchar(128) NOT NULL DEFAULT '' COMMENT '把拥有的权限的id信息通过 ,逗号 连接为字符串，权限ids：1,2,5',
  `role_ps_ca` text COMMENT '控制器-操作,控制器-操作,控制器-操作',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '记录删除时间，假(逻辑)删除',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sp_role`
--

LOCK TABLES `sp_role` WRITE;
/*!40000 ALTER TABLE `sp_role` DISABLE KEYS */;
INSERT INTO `sp_role` VALUES (30,'主管','101,104,102,107,113','Goods-index,Order-index,Order-xiugai',1532313302,1532334306,NULL),(31,'经理5','101,104,105,106','Goods-index,Brand-index,Category-index',1532313302,1532333788,NULL);
/*!40000 ALTER TABLE `sp_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-07-30 11:14:53
