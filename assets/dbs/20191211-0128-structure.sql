USE `bumacima_simtr`;
-- MariaDB dump 10.17  Distrib 10.4.8-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: bumacima_simtr
-- ------------------------------------------------------
-- Server version	10.4.8-MariaDB

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
-- Table structure for table `tbl_afd`
--

DROP TABLE IF EXISTS `tbl_afd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_afd` (
  `id_afd` int(11) NOT NULL AUTO_INCREMENT,
  `id_subbagian` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_afd` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_afd`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_aktivitas`
--

DROP TABLE IF EXISTS `tbl_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_aktivitas` (
  `id_aktivitas` int(11) NOT NULL AUTO_INCREMENT,
  `tstr` varchar(5) DEFAULT NULL,
  `tahun_giling` int(11) DEFAULT NULL,
  `nama_aktivitas` varchar(45) DEFAULT NULL,
  `biaya` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_aktivitas`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_bagian`
--

DROP TABLE IF EXISTS `tbl_bagian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_bagian` (
  `id_bagian` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bagian` varchar(45) DEFAULT NULL,
  `id_kepala_bagian` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_bagian`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_dokumen`
--

DROP TABLE IF EXISTS `tbl_dokumen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_dokumen` (
  `id_dokumen` int(11) NOT NULL AUTO_INCREMENT,
  `no_dokumen` varchar(45) DEFAULT NULL,
  `tipe_dokumen` varchar(45) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `id_bagian` int(11) DEFAULT NULL,
  `id_subbagian` int(11) DEFAULT NULL,
  `tgl_dokumen` date DEFAULT NULL,
  `kode_rekening` varchar(45) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `tgl_buat` datetime(6) DEFAULT NULL,
  `tgl_validasi_bagian` datetime(6) DEFAULT NULL,
  `tgl_validasi_tuk` datetime(6) DEFAULT NULL,
  `tgl_validasi_gm` datetime(6) DEFAULT NULL,
  `tgl_terima_tuk` datetime(6) DEFAULT NULL,
  `tgl_terima_gm` datetime(6) DEFAULT NULL,
  `tgl_reject_tuk` datetime(6) DEFAULT NULL,
  `tgl_reject_gm` datetime(6) DEFAULT NULL,
  PRIMARY KEY (`id_dokumen`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_dosis`
--

DROP TABLE IF EXISTS `tbl_dosis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_dosis` (
  `id_dosis` int(11) NOT NULL AUTO_INCREMENT,
  `id_aktivitas` int(11) DEFAULT NULL,
  `id_bahan` int(11) DEFAULT NULL,
  `dosis` decimal(7,2) DEFAULT NULL,
  PRIMARY KEY (`id_dosis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_objek`
--

DROP TABLE IF EXISTS `tbl_objek`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_objek` (
  `id_objek` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_objek` varchar(45) DEFAULT NULL,
  `nama_objek` varchar(45) DEFAULT NULL,
  `deskripsi_1` varchar(255) DEFAULT NULL,
  `deskripsi_2` varchar(255) DEFAULT NULL,
  `satuan` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_objek`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_simtr_bahan`
--

DROP TABLE IF EXISTS `tbl_simtr_bahan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_bahan` (
  `id_bahan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bahan` varchar(45) DEFAULT NULL,
  `jenis_bahan` varchar(45) DEFAULT NULL,
  `satuan` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_bahan`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_simtr_geocode`
--

DROP TABLE IF EXISTS `tbl_simtr_geocode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_geocode` (
  `id_geocode` int(11) NOT NULL AUTO_INCREMENT,
  `id_petani` int(11) DEFAULT NULL,
  `trackpoint` point DEFAULT NULL,
  PRIMARY KEY (`id_geocode`)
) ENGINE=InnoDB AUTO_INCREMENT=2396 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_simtr_kelompoktani`
--

DROP TABLE IF EXISTS `tbl_simtr_kelompoktani`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_kelompoktani` (
  `id_kelompok` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelompok` varchar(100) DEFAULT NULL,
  `no_ktp` varchar(45) DEFAULT NULL,
  `no_kontrak` varchar(100) DEFAULT NULL,
  `id_desa` varchar(15) DEFAULT NULL,
  `mt` varchar(5) DEFAULT NULL,
  `tahun_giling` int(11) DEFAULT NULL,
  `kategori` int(11) DEFAULT NULL,
  `id_varietas` varchar(10) DEFAULT NULL,
  `scan_ktp` blob DEFAULT NULL,
  `scan_kk` blob DEFAULT NULL,
  `scan_surat` blob DEFAULT NULL,
  PRIMARY KEY (`id_kelompok`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `bumacima_simtr`.`tbl_simtr_kelompoktani_AFTER_INSERT` AFTER INSERT ON `tbl_simtr_kelompoktani` FOR EACH ROW
BEGIN

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tbl_simtr_masatanam`
--

DROP TABLE IF EXISTS `tbl_simtr_masatanam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_masatanam` (
  `masa_tanam` varchar(4) NOT NULL,
  PRIMARY KEY (`masa_tanam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_simtr_petani`
--

DROP TABLE IF EXISTS `tbl_simtr_petani`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_petani` (
  `id_petani` int(11) NOT NULL AUTO_INCREMENT,
  `id_kelompok` int(11) DEFAULT NULL,
  `nama_petani` varchar(255) DEFAULT NULL,
  `luas` double(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_petani`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_simtr_transaksi`
--

DROP TABLE IF EXISTS `tbl_simtr_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `id_bahan` int(11) DEFAULT NULL,
  `id_kelompoktani` int(11) DEFAULT NULL,
  `id_vendor` int(11) DEFAULT NULL,
  `id_aktivitas` int(11) DEFAULT NULL,
  `no_transaksi` varchar(45) DEFAULT NULL,
  `kode_transaksi` int(11) DEFAULT NULL,
  `kuanta` int(11) DEFAULT NULL,
  `rupiah` int(11) DEFAULT NULL,
  `tgl_transaksi` datetime DEFAULT NULL,
  `catatan` varchar(255) DEFAULT NULL,
  `tahun_giling` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_transaksi`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `bumacima_simtr`.`tbl_simtr_persediaan_BEFORE_INSERT` BEFORE INSERT ON `tbl_simtr_transaksi` FOR EACH ROW
BEGIN
	DECLARE auto_inc integer;
    SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tbl_simtr_persediaan' INTO auto_inc;
	set new.no_transaksi = concat(new.kode_transaksi,new.tahun_giling,YEAR(now()),LPAD(MONTH(now()),2,'0'),LPAD(DAY(now()),2,'0'),LPAD(HOUR(now()),2,'0'),LPAD(MINUTE(now()),2,'0'),LPAD(SECOND(now()),2,'0'));
    set new.tgl_transaksi = now();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tbl_simtr_vendor`
--

DROP TABLE IF EXISTS `tbl_simtr_vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_vendor` (
  `id_vendor` int(11) NOT NULL AUTO_INCREMENT,
  `nama_vendor` varchar(100) DEFAULT NULL,
  `npwp_vendor` varchar(45) DEFAULT NULL,
  `alamat_vendor` varchar(255) DEFAULT NULL,
  `alamat_2_vendor` varchar(255) DEFAULT NULL,
  `nama_kontak` varchar(45) DEFAULT NULL,
  `telp_kontak` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_vendor`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_simtr_wilayah`
--

DROP TABLE IF EXISTS `tbl_simtr_wilayah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_simtr_wilayah` (
  `id_wilayah` varchar(20) NOT NULL,
  `nama_wilayah` varchar(100) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_wilayah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_subbagian`
--

DROP TABLE IF EXISTS `tbl_subbagian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_subbagian` (
  `id_subbagian` int(11) NOT NULL AUTO_INCREMENT,
  `id_bagian` int(11) DEFAULT NULL,
  `nama_subbagian` varchar(45) DEFAULT NULL,
  `id_kepala_subbagian` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_subbagian`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_uraian`
--

DROP TABLE IF EXISTS `tbl_uraian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_uraian` (
  `id_uraian` int(11) NOT NULL AUTO_INCREMENT,
  `id_dokumen` int(11) DEFAULT NULL,
  `id_objek` int(11) DEFAULT NULL,
  `jml_dibutuhkan` int(11) DEFAULT NULL,
  `estimasi_harga_satuan` int(11) DEFAULT NULL,
  `estimasi_jml_harga` int(11) DEFAULT NULL,
  `sumber_harga` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_uraian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `jabatan` varchar(45) DEFAULT NULL,
  `nama_user` varchar(45) DEFAULT NULL,
  `uname` varchar(255) DEFAULT NULL,
  `pwd` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_varietas`
--

DROP TABLE IF EXISTS `tbl_varietas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_varietas` (
  `id_varietas` varchar(5) NOT NULL,
  `nama_varietas` varchar(255) DEFAULT NULL,
  `tipe_kemasakan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_varietas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-12-11  1:23:26
