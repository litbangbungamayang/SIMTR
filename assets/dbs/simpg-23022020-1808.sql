-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 23, 2020 at 12:08 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simpg`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `get_hablur_koreksi`$$
CREATE DEFINER=`root`@`%` PROCEDURE `get_hablur_koreksi` (`pFaktor` DECIMAL(3,2), `pTgl` DATE)  BEGIN
	select
		round(sum(
			if ( cs.RAFAKSI > 0,
				(6/100)*(timb.netto_final/1000) - (cs.RAFAKSI/100)*(6/100)*(timb.netto_final/1000),
				if ( (((cs.KNPP*cs.NNPP)/100)*(timb.netto_final/1000)*pFaktor)/(timb.netto_final/1000) < 0.06,
					(6/100)*(timb.netto_final/1000),
					((cs.KNPP*cs.NNPP)/100)*(timb.netto_final/1000)*pFaktor
				)
			)
		),2)
		as hablur_efektif
	from TBL_CORELAB cs 
		join t_spta spta on cs.NUMERATOR = spta.no_spat 
		join t_timbangan timb on timb.id_spat = spta.id 
	where spta.tgl_timbang = pTgl
		and spta.kode_kat_lahan = 'TR-KR'
	group by spta.tgl_timbang;
END$$

DROP PROCEDURE IF EXISTS `get_rend_cs`$$
CREATE DEFINER=`root`@`%` PROCEDURE `get_rend_cs` (`pTgl` DATE)  BEGIN
	select round(sum(cs.NNPP*cs.KNPP*timb.netto_final/1000/100),2) as hablur_analisa, round(sum(timb.netto_final/1000),2) as ton_tebu,
		round(sum(cs.NNPP*cs.KNPP*timb.netto_final/1000/100)/sum(timb.netto_final/1000)*100,2) as rend 
	from TBL_CORELAB cs 
		join t_spta spta on cs.NUMERATOR = spta.no_spat 
		join t_timbangan timb on timb.id_spat = spta.id 
	where spta.tgl_timbang = pTgl and spta.kode_kat_lahan = 'TR-KR' 
	group by spta.tgl_timbang;
END$$

DROP PROCEDURE IF EXISTS `get_tebu_likuidasi`$$
CREATE DEFINER=`root`@`%` PROCEDURE `get_tebu_likuidasi` (`pTgl1` DATE, `pTgl2` DATE)  BEGIN
	select
		tmat.no_tiket,
		tmat.no_kendaraan,
		tmat.tgl_timbang_1,
		tmat.tgl_timbang_2,
		tmat.timbang_1,
		tmat.timbang_2,
		tmat.netto
	from t_timbang_material tmat
	where 
		tmat.nama_material = 'Sampah CY'
		and tmat.tgl_timbang_2 >= pTgl1
        and tmat.tgl_timbang_2 <= pTgl2
        and tmat.netto > 0;
END$$

DROP PROCEDURE IF EXISTS `monitoring_integrasi`$$
CREATE DEFINER=`root`@`%` PROCEDURE `monitoring_integrasi` (`tgl` DATE)  BEGIN
select
	jam.jam,
	selektor.rit as selektor,
    timbangan.netto as timbangan,
    giling.ton_giling as digiling
from t_lap_jam jam
	left join (
		select hour(spta.selektor_tgl) as jam, count(sel.id_selektor) as rit
        from t_selektor sel
			join t_spta spta on spta.id = sel.id_spta
		where sel.tgl_urut = tgl
        group by hour(spta.selektor_tgl)
    ) as selektor on selektor.jam = jam.jam
    left join (
		select hour(spta.timb_netto_tgl) as jam, sum(timb.netto_final/1000) as netto 
		from t_spta spta
			join t_timbangan timb on spta.id = timb.id_spat
		where spta.tgl_timbang = tgl
        group by hour(spta.timb_netto_tgl)
    ) as timbangan on timbangan.jam = jam.jam
    left join (
		select hour(spta.meja_tebu_tgl) as jam, sum(timb.netto_final/1000) as ton_giling 
		from t_meja_tebu mt
			join t_spta spta on spta.id = mt.id_spta
            join t_timbangan timb on timb.id_spat = mt.id_spta
		where spta.tgl_giling = tgl
        group by hour(spta.meja_tebu_tgl)
    ) as giling on giling.jam = jam.jam
    order by case
		when jam.jam = '06' then 1
		when jam.jam = '07' then 2
        when jam.jam = '08' then 3
        when jam.jam = '09' then 4
        when jam.jam = '10' then 5
        when jam.jam = '11' then 6
        when jam.jam = '12' then 7
        when jam.jam = '13' then 8
        when jam.jam = '14' then 9
        when jam.jam = '15' then 10
        when jam.jam = '16' then 11
        when jam.jam = '17' then 12
        when jam.jam = '18' then 13
        when jam.jam = '19' then 14
        when jam.jam = '20' then 15
        when jam.jam = '21' then 16
        when jam.jam = '22' then 17
        when jam.jam = '23' then 18
        when jam.jam = '00' then 19
        when jam.jam = '01' then 20
        when jam.jam = '02' then 21
        when jam.jam = '03' then 22
        when jam.jam = '04' then 23
        when jam.jam = '05' then 24
	end;
END$$

DROP PROCEDURE IF EXISTS `monitoring_pasok`$$
CREATE DEFINER=`root`@`%` PROCEDURE `monitoring_pasok` (IN `pPeriodeAwal` DATETIME, IN `pPeriodeAkhir` DATETIME)  BEGIN
	select
		(
			select case fld.kepemilikan
				when 'ts-hg' then 'TS'
				when 'tr-kr' then 'TR'
                when 'ts-ip' then 'TSI'
			end as rayon
		) as tstr,
		(
			select case fld.kepemilikan
				when 'ts-hg' then mid(fld.deskripsi_blok,4,2)
				when 'tr-kr' then 'TR'
                when 'ts-ip' then 'TSI'
			end as rayon
		) as rayon,
		right(fld.divisi,2) as afdeling,
		(
			select count(sub_spta.id) 
			from simpg.t_spta sub_spta 
			where sub_spta.kode_affd = spta.kode_affd and
			sub_spta.selektor_status = 1 and
			sub_spta.selektor_tgl >= pPeriodeAwal and
            sub_spta.selektor_tgl < pPeriodeAkhir and
			sub_spta.timb_bruto_status = 0
		) as antrian,
		(
			select count(sub_spta.id)
			from simpg.t_spta sub_spta 
			where sub_spta.kode_affd = spta.kode_affd and
			sub_spta.selektor_status = 1 and
			sub_spta.selektor_tgl >= pPeriodeAwal and
            sub_spta.selektor_tgl < pPeriodeAkhir and
			sub_spta.timb_bruto_status = 1 and
			sub_spta.timb_netto_status = 0
		) as caneyard,
		(
			select sum(sub_spta.selektor_status)
			from simpg.t_spta sub_spta 
			where sub_spta.kode_affd = spta.kode_affd and
			sub_spta.selektor_status = 1 and
            sub_spta.selektor_tgl >= pPeriodeAwal and
            sub_spta.selektor_tgl < pPeriodeAkhir
		) as totalrit,
		sum(round(timb.netto_final/1000,2)) as netto
	from simpg.t_spta spta
	inner join simpg.sap_field fld on spta.kode_blok = fld.kode_blok
	inner join simpg.t_timbangan timb on spta.id = timb.id_spat
	inner join simpg.t_selektor sel on sel.id_spta = spta.id
	where spta.selektor_status = 1 
		and spta.selektor_tgl >= pPeriodeAwal
		and spta.selektor_tgl < pPeriodeAkhir
	group by afdeling;
END$$

DROP PROCEDURE IF EXISTS `monitoring_pasok_metode`$$
CREATE DEFINER=`root`@`%` PROCEDURE `monitoring_pasok_metode` ()  BEGIN
select * from(
select 
	concat(left(fld.deskripsi_blok,6),'-',spta.metode_tma) as idrayon,
	left(fld.deskripsi_blok,6) as rayon,
    if(spta.metode_tma = 1,'MANUAL',if(spta.metode_tma = 2,'SM','MEKANIS')) as tebangan,
    count(if(spta.selektor_status = 1 AND spta.timb_bruto_status = 0,1,NULL)) as antrian,
    count(if(spta.timb_bruto_status = 1 AND spta.timb_netto_status = 0,1,NULL)) as caneyard,
    count(if(spta.selektor_status = 1,1,NULL)) as total_rit
from t_spta spta
    inner join sap_field fld on fld.kode_blok = spta.kode_blok
where 
	spta.tgl_spta = if(curtime() >= '00:00' and curtime() < '06:00' ,date_add(curdate(),interval -1 day),curdate())
    and spta.kode_kat_lahan = 'TS-HG'
group by rayon,spta.metode_tma
order by rayon,spta.metode_tma
) a inner join
(
select
	concat(left(fld.deskripsi_blok,6),'-',spta.metode_tma) as idrayon,
    left(fld.deskripsi_blok,6) as rayon,
    sum(round(timb.netto_final/1000,2)) as netto
from t_spta spta
    inner join sap_field fld on fld.kode_blok = spta.kode_blok
    inner join t_timbangan timb on timb.id_spat = spta.id
where 
	spta.tgl_spta = if(curtime() >= '00:00' and curtime() < '06:00' ,date_add(curdate(),interval -1 day),curdate())
    and spta.kode_kat_lahan = 'TS-HG'
group by rayon,spta.metode_tma
order by rayon,spta.metode_tma
) b on a.idrayon = b.idrayon;
END$$

DROP PROCEDURE IF EXISTS `monitoring_pasok_rayon`$$
CREATE DEFINER=`root`@`%` PROCEDURE `monitoring_pasok_rayon` (`pPeriodeAwal` DATETIME, `pPeriodeAkhir` DATETIME)  BEGIN
	select
		(
			select case fld.kepemilikan
				when 'ts-hg' then 'TS'
				when 'tr-kr' then 'TR'
                when 'ts-ip' then 'TSI'
			end as rayon
		) as tstr,
		(
			select case fld.kepemilikan
				when 'ts-hg' then left(fld.deskripsi_blok,6)
				when 'tr-kr' then 'TR'
                when 'ts-ip' then 'TSI'
			end as rayon
		) as rayon,
		(
			select count(sub_spta.id) 
			from simpg.t_spta sub_spta 
			inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then left(sub_fld.deskripsi_blok,6)
							when 'tr-kr' then 'TR'
                            when 'ts-ip' then 'TSI'
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then left(fld.deskripsi_blok,6)
								when 'tr-kr' then 'TR'
                                when 'ts-ip' then 'TSI'
							end as rayon
						) and
			sub_spta.selektor_status = 1 and
			sub_spta.selektor_tgl >= pPeriodeAwal and
            sub_spta.selektor_tgl < pPeriodeAkhir and
			sub_spta.timb_bruto_status = 0
		) as antrian,
		(
			select count(sub_spta.id)
			from simpg.t_spta sub_spta
            inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then left(sub_fld.deskripsi_blok,6)
							when 'tr-kr' then 'TR'
                            when 'ts-ip' then 'TSI'
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then left(fld.deskripsi_blok,6)
								when 'tr-kr' then 'TR'
                                when 'ts-ip' then 'TSI'
							end as rayon
						) and
			sub_spta.selektor_status = 1 and
			sub_spta.selektor_tgl >= pPeriodeAwal and
            sub_spta.selektor_tgl < pPeriodeAkhir and
			sub_spta.timb_bruto_status = 1 and
			sub_spta.timb_netto_status = 0
		) as caneyard,
		(
			select sum(sub_spta.selektor_status)
			from simpg.t_spta sub_spta
            inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then left(sub_fld.deskripsi_blok,6)
							when 'tr-kr' then 'TR'
                            when 'ts-ip' then 'TSI'
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then left(fld.deskripsi_blok,6)
								when 'tr-kr' then 'TR'
                                when 'ts-ip' then 'TSI'
							end as rayon
						) and
			sub_spta.selektor_status = 1 and
            sub_spta.selektor_tgl >= pPeriodeAwal and
            sub_spta.selektor_tgl < pPeriodeAkhir
		) as totalrit,
		sum(round(timb.netto_final/1000,2)) as netto
	from simpg.t_spta spta
	inner join simpg.sap_field fld on spta.kode_blok = fld.kode_blok
	inner join simpg.t_timbangan timb on spta.id = timb.id_spat
	inner join simpg.t_selektor sel on sel.id_spta = spta.id
	where spta.selektor_status = 1 
		and spta.selektor_tgl >= pPeriodeAwal
		and spta.selektor_tgl < pPeriodeAkhir
	group by rayon;
END$$

DROP PROCEDURE IF EXISTS `monitoring_pasok_tr`$$
CREATE DEFINER=`root`@`%` PROCEDURE `monitoring_pasok_tr` ()  BEGIN
select * from (
select
	fld.divisi as afdeling,
    count(if(spta.selektor_status = 1 AND spta.timb_bruto_status = 0,1,NULL)) as antrian,
    count(if(spta.timb_bruto_status = 1 AND spta.timb_netto_status = 0,1,NULL)) as caneyard,
    count(if(spta.selektor_status = 1,1,NULL)) as total_rit
from t_spta spta
	inner join sap_field fld on fld.kode_blok = spta.kode_blok
where
	spta.tgl_spta = if(curtime() >= '00:00' and curtime() < '06:00' ,date_add(curdate(),interval -1 day),curdate())
    and spta.kode_kat_lahan = 'TR-KR'
group by afdeling
) a inner join
(
select
	fld.divisi as afdeling,
    sum(round(timb.netto_final/1000,2)) as netto
from t_spta spta
	inner join sap_field fld on fld.kode_blok = spta.kode_blok
    inner join t_timbangan timb on timb.id_spat = spta.id
where
	spta.tgl_spta = if(curtime() >= '00:00' and curtime() < '06:00' ,date_add(curdate(),interval -1 day),curdate())
    and spta.kode_kat_lahan = 'TR-KR'
group by afdeling
) b on a.afdeling = b.afdeling;
END$$

DROP PROCEDURE IF EXISTS `pasok_test`$$
CREATE DEFINER=`root`@`%` PROCEDURE `pasok_test` (`pPeriodeAwal` DATETIME, `pPeriodeAkhir` DATETIME)  BEGIN
	select
		(
			select case fld.kepemilikan
				when 'ts-hg' then 'TS'
				when 'tr-kr' then 'TR'
                when 'ts-ip' then 'TSI'
			end as rayon
		) as tstr,
		(
			select case fld.kepemilikan
				when 'ts-hg' then right(fld.deskripsi_blok,1)
				when 'tr-kr' then 'TR'
                when 'ts-ip' then 'TSI'
			end as rayon
		) as rayon,
		(
			select count(sub_spta.id) 
			from simpg.t_spta sub_spta 
			inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then right(sub_fld.deskripsi_blok,1)
							when 'tr-kr' then 'TR'
                            when 'ts-ip' then 'TSI'
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then right(fld.deskripsi_blok,1)
								when 'tr-kr' then 'TR'
                                when 'ts-ip' then 'TSI'
							end as rayon
						) and
			sub_spta.selektor_status = 1 and
			sub_spta.timb_bruto_status = 0
		) as antrian,
		(
			select count(sub_spta.id)
			from simpg.t_spta sub_spta
            inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then right(sub_fld.deskripsi_blok,1)
							when 'tr-kr' then 'TR'
                            when 'ts-ip' then 'TSI'
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then right(fld.deskripsi_blok,1)
								when 'tr-kr' then 'TR'
                                when 'ts-ip' then 'TSI'
							end as rayon
						) and
			sub_spta.selektor_status = 1 and
			sub_spta.timb_bruto_status = 1 and
			sub_spta.timb_netto_status = 0
		) as caneyard,
		(
			select sum(sub_spta.selektor_status)
			from simpg.t_spta sub_spta
            inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then right(sub_fld.deskripsi_blok,1)
							when 'tr-kr' then 'TR'
                            when 'ts-ip' then 'TSI'
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then right(fld.deskripsi_blok,1)
								when 'tr-kr' then 'TR'
                                when 'ts-ip' then 'TSI'
							end as rayon
						) and
			sub_spta.selektor_status = 1
		) as totalrit,
		sum(round(timb.netto_final/1000,2)) as netto
	from simpg.t_spta spta
	inner join simpg.sap_field fld on spta.kode_blok = fld.kode_blok
	inner join simpg.t_timbangan timb on spta.id = timb.id_spat
	inner join simpg.t_selektor sel on sel.id_spta = spta.id
	where spta.selektor_status = 1 
		and spta.selektor_tgl < pPeriodeAkhir
        and spta.selektor_tgl >= pPeriodeAwal
	group by rayon;
END$$

DROP PROCEDURE IF EXISTS `sifat_kemasakan`$$
CREATE DEFINER=`root`@`%` PROCEDURE `sifat_kemasakan` (`p_TglTebu` DATE, `p_TglTebu2` DATE)  BEGIN
	drop table if exists tempSifat;
    create table tempSifat as
	select
		(case when spta.kode_kat_lahan = 'TS-HG' then 'TS' else case when spta.kode_kat_lahan = 'TR-KR' then 'TR' else 'TSI' end end) as TSTR,
		cast(count(case when varteb.sifat = 'AWAL' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masakawal,
		cast(count(case when varteb.sifat = 'TENGAH' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaktengah,
		cast(count(case when varteb.sifat = 'LAMBAT' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaklambat,
		count(spta.id) as 'TOTAL'
	from t_spta spta
		inner join sap_field fld on spta.kode_blok = fld.kode_blok
		inner join sap_m_varietas varteb on fld.kode_varietas = varteb.id_varietas
	where spta.timb_netto_status = 1 and spta.tgl_spta = p_TglTebu
    group by spta.kode_kat_lahan union
    select
		'BUMA' as TSTR,
		cast(count(case when varteb.sifat = 'AWAL' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masakawal,
		cast(count(case when varteb.sifat = 'TENGAH' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaktengah,
		cast(count(case when varteb.sifat = 'LAMBAT' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaklambat,
		count(spta.id) as 'TOTAL'
	from t_spta spta
		inner join sap_field fld on spta.kode_blok = fld.kode_blok
		inner join sap_m_varietas varteb on fld.kode_varietas = varteb.id_varietas
	where spta.timb_netto_status = 1 and spta.tgl_spta >= p_TglTebu and spta.tgl_spta <= p_TglTebu2;
END$$

DROP PROCEDURE IF EXISTS `sifat_kemasakan_sd`$$
CREATE DEFINER=`root`@`%` PROCEDURE `sifat_kemasakan_sd` (`tgl1` DATE, `tgl2` DATE)  BEGIN
	drop table if exists tempSifat;
    create table tempSifat as
	select
		(case when spta.kode_kat_lahan = 'TS-HG' then 'TS' else case when spta.kode_kat_lahan = 'TR-KR' then 'TR' else 'TSI' end end) as TSTR,
		cast(count(case when varteb.sifat = 'AWAL' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masakawal,
		cast(count(case when varteb.sifat = 'TENGAH' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaktengah,
		cast(count(case when varteb.sifat = 'LAMBAT' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaklambat,
		count(spta.id) as 'TOTAL'
	from t_spta spta
		inner join sap_field fld on spta.kode_blok = fld.kode_blok
		inner join sap_m_varietas varteb on fld.kode_varietas = varteb.id_varietas
	where spta.timb_netto_status = 1 and spta.tgl_spta >= tgl1 and spta.tgl_spta <= tgl2
    group by spta.kode_kat_lahan union
    select
		'BUMA' as TSTR,
		cast(count(case when varteb.sifat = 'AWAL' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masakawal,
		cast(count(case when varteb.sifat = 'TENGAH' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaktengah,
		cast(count(case when varteb.sifat = 'LAMBAT' then spta.id end)/count(spta.id)*100 as decimal(4,1)) as masaklambat,
		count(spta.id) as 'TOTAL'
	from t_spta spta
		inner join sap_field fld on spta.kode_blok = fld.kode_blok
		inner join sap_m_varietas varteb on fld.kode_varietas = varteb.id_varietas
	where spta.timb_netto_status = 1 and spta.tgl_spta >= tgl1 and spta.tgl_spta <= tgl2;
END$$

DROP PROCEDURE IF EXISTS `sisa_tebu`$$
CREATE DEFINER=`root`@`%` PROCEDURE `sisa_tebu` ()  BEGIN
select a-b-1869.41 as sisatebu from
(
	select (
		select
			round(sum(timb.netto_final)/1000,2) as tebumasuk
		from t_timbangan timb
		where 
			#timb.tgl_netto >= if(curtime() >= '00:00' and curtime() < '06:00', date_add(date_add(curdate(), interval -2 day), interval 6 hour),
			#date_add(date_add(curdate(), interval -1 day), interval 6 hour))
			timb.tgl_netto < if(curtime() >= '00:00' and curtime() < '06:00', date_add(date_add(curdate(), interval -1 day), interval 6 hour),
			date_add(curdate(), interval 6 hour))
		) as a, 
		(
			select
				round(sum(timb.netto_final)/1000,2) as tebugiling
			from t_meja_tebu mt
				inner join t_timbangan timb on timb.id_spat = mt.id_spta
			where 
				#mt.tgl_meja_tebu >= if(curtime() >= '00:00' and curtime() < '06:00', date_add(date_add(curdate(), interval -2 day), interval 6 hour),
				#date_add(date_add(curdate(), interval -1 day), interval 6 hour))
				mt.tgl_meja_tebu < if(curtime() >= '00:00' and curtime() < '06:00', date_add(date_add(curdate(), interval -1 day), interval 6 hour),
				date_add(curdate(), interval 6 hour))
		) as b
	) as jumlah;
END$$

DROP PROCEDURE IF EXISTS `tebu_giling`$$
CREATE DEFINER=`root`@`%` PROCEDURE `tebu_giling` ()  BEGIN
select
	round(sum(timb.netto_final)/1000,2) as tebugiling
from t_meja_tebu mt
	inner join t_timbangan timb on timb.id_spat = mt.id_spta
where 
	mt.tgl_meja_tebu  > if(curtime() >= '00:00' and curtime() < '06:00' ,
    date_add(date_add(curdate(),interval -1 day),interval 6 hour),date_add(curdate(),interval 6 hour));
END$$

DROP PROCEDURE IF EXISTS `tebu_masuk`$$
CREATE DEFINER=`root`@`%` PROCEDURE `tebu_masuk` ()  BEGIN
	select
		sum(round(timb.netto/1000,2)) as netto
	from t_timbangan timb
	where
		timb.tgl_netto > if(curtime() >= '00:00' and curtime() < '06:00' ,
		date_add(date_add(curdate(),interval -1 day),interval 6 hour),date_add(curdate(),interval 6 hour));
END$$

DROP PROCEDURE IF EXISTS `temp_monitoring_pasok`$$
CREATE DEFINER=`root`@`%` PROCEDURE `temp_monitoring_pasok` (IN `tglAwal` DATETIME)  BEGIN
	select
		(
			select case fld.kepemilikan
				when 'ts-hg' then 'TS'
				when 'tr-kr' then 'TR'
                when 'ts-ip' then 'TSI'
			end as rayon
		) as tstr,
		(
			select case fld.kepemilikan
				when 'ts-hg' then left (fld.deskripsi_blok,6)
				when 'tr-kr' then fld.divisi
                when 'ts-ip' then fld.divisi
			end as rayon
		) as rayon,
		(
			select count(sub_spta.id) 
			from simpg.t_spta sub_spta 
			inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then left(sub_fld.deskripsi_blok,6)
							when 'tr-kr' then sub_fld.divisi
                            when 'ts-ip' then sub_fld.divisi
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then left(fld.deskripsi_blok,6)
								when 'tr-kr' then fld.divisi
                                when 'ts-ip' then fld.divisi
							end as rayon
						) and
			sub_spta.selektor_status = 1 and
			sub_spta.timb_bruto_status = 0 and
            sub_spta.selektor_tgl >= (tglAwal - interval 1 day + interval 6 hour)
		) as antrian,
		(
			select count(sub_spta.id)
			from simpg.t_spta sub_spta
            inner join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then left(sub_fld.deskripsi_blok,6)
							when 'tr-kr' then sub_fld.divisi
                            when 'ts-ip' then sub_fld.divisi
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then left(fld.deskripsi_blok,6)
								when 'tr-kr' then fld.divisi
                                when 'ts-ip' then fld.divisi
							end as rayon
						) and
			sub_spta.selektor_status = 1 and
			sub_spta.timb_bruto_status = 1 and
            sub_spta.tgl_spta >= (tglAwal - interval 1 day) and
			sub_spta.timb_netto_status = 0
		) as caneyard,
		count(spta.id) as totalrit,
		(
			select sum(sub_timb.netto_final/1000)
			from simpg.t_spta sub_spta
            join simpg.sap_field sub_fld on sub_spta.kode_blok = sub_fld.kode_blok
            join simpg.t_timbangan sub_timb on sub_timb.id_spat = sub_spta.id
			where (
						select case sub_fld.kepemilikan
							when 'ts-hg' then left(sub_fld.deskripsi_blok,6)
							when 'tr-kr' then sub_fld.divisi
                            when 'ts-ip' then sub_fld.divisi
						end as sub_rayon
					) = (
							select case fld.kepemilikan
								when 'ts-hg' then left(fld.deskripsi_blok,6)
								when 'tr-kr' then fld.divisi
                                when 'ts-ip' then fld.divisi
							end as rayon
						) and
			sub_spta.tgl_timbang = tglAwal
		) as netto
	from simpg.t_spta spta
	inner join simpg.sap_field fld on spta.kode_blok = fld.kode_blok
	inner join simpg.t_selektor sel on sel.id_spta = spta.id
	where 
			(spta.selektor_tgl >= (tglAwal - interval 2 day + interval 6 hour)
				and spta.timb_bruto_tgl >= (tglAwal + interval 6 hour)) 
			or spta.tgl_timbang = tglAwal
			or (spta.selektor_tgl >= (tglAwal - interval 2 day + interval 6 hour)
				and spta.timb_bruto_status = 0)
	group by rayon order by 
		case rayon 
			when 'RY I' then 1
            when 'RY II' then 2
            when 'RY III' then 3
            when 'RY IV' then 4
            when 'TUBU' then 5
            when 'BEKRI' then 6
            when 'AFD12' then 7
            when 'AFD13' then 8
            when 'AFD14' then 9
            when 'RY LIT' then 10
		end;
END$$

DROP PROCEDURE IF EXISTS `umur_sifat`$$
CREATE DEFINER=`root`@`%` PROCEDURE `umur_sifat` (`pTglTebu` DATE)  BEGIN
	call umur_tebu(pTglTebu,pTglTebu);
    call sifat_kemasakan(pTglTebu,pTglTebu);
    select tempSifat.tstr,tempUmur.umur_a, tempUmur.umur_b, tempUmur.umur_c, tempUmur.umur_d, tempUmur.ratarata,
		tempSifat.masakawal, tempSifat.masaktengah, tempSifat.masaklambat
    from tempUmur
    left join tempSifat on tempUmur.tstr = tempSifat.tstr;
END$$

DROP PROCEDURE IF EXISTS `umur_sifat_sd`$$
CREATE DEFINER=`root`@`%` PROCEDURE `umur_sifat_sd` (`pTglTebu` DATE, `pTglTebu2` DATE)  BEGIN
	call umur_tebu_sd(pTglTebu,pTglTebu2);
    call sifat_kemasakan_sd(pTglTebu,pTglTebu2);
    select tempSifat.tstr,tempUmur.umur_a, tempUmur.umur_b, tempUmur.umur_c, tempUmur.umur_d, tempUmur.ratarata,
		tempSifat.masakawal, tempSifat.masaktengah, tempSifat.masaklambat
    from tempUmur
    left join tempSifat on tempUmur.tstr = tempSifat.tstr;
END$$

DROP PROCEDURE IF EXISTS `umur_tebu`$$
CREATE DEFINER=`root`@`%` PROCEDURE `umur_tebu` (`p_TglTebu` DATE, `p_Tglebu2` DATE)  BEGIN
    declare nilaiSkrg int;
    
    if (day(CURDATE()) < 16)
    then select(month(curdate())*2)-1 into nilaiSkrg;
    else select(month(curdate())*2) into nilaiSkrg;
    end if;
    drop table if exists tempUmur;
    create table tempUmur as
    select
		(case when kode_kat_lahan = 'TS-HG' then 'TS' else case when kode_kat_lahan = 'TR-KR' then 'TR' else 'TSI' end end) as tstr,
		cast(sum(case when umurBulan < 10 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_a,
        cast(sum(case when (umurBulan < 11 && umurBulan >= 10) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_b,
        cast(sum(case when (umurBulan < 12 && umurBulan >= 11) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_c,
        cast(sum(case when umurBulan >= 12 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_d,
        cast(sum(nilaiBulan)/sum(jmlNetto) as decimal(4,2)) as rataRata,
        cast(sum(jmlNetto) as decimal(6,2)) as tebumasuk
    from
    (
		select
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-nilaiSkrg)*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,1)*2)-nilaiSkrg))*0.5)*timb.netto/1000
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-nilaiSkrg)*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,2)*2)-nilaiSkrg))*0.5)*timb.netto/1000
                end
			end
            ) as nilaiBulan,
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-nilaiSkrg)*0.5)
                else (12-(((left(s_field.periode,1)*2)-nilaiSkrg))*0.5)
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-nilaiSkrg)*0.5)
                else (12-(((left(s_field.periode,2)*2)-nilaiSkrg))*0.5)
                end
			end
            ) as umurBulan,
            (timb.netto/1000) as jmlNetto,
            spta.kode_kat_lahan
		from t_timbangan timb
		inner join t_spta spta on spta.id = timb.id_spat
		inner join sap_field s_field on s_field.kode_blok = spta.kode_blok
		where spta.tgl_spta >= p_TglTebu and spta.tgl_spta <= p_Tglebu2
	) as total
    group by kode_kat_lahan
    union
    select
		'BUMA',
		cast(sum(case when umurBulan < 10 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_a,
        cast(sum(case when (umurBulan < 11 && umurBulan >= 10) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_b,
        cast(sum(case when (umurBulan < 12 && umurBulan >= 11) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_c,
        cast(sum(case when umurBulan >= 12 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_d,
        cast(sum(nilaiBulan)/sum(jmlNetto) as decimal(4,2)) as rataRata,
        cast(sum(jmlNetto) as decimal(6,2)) as tebumasuk
    from
    (
		select
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-nilaiSkrg)*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,1)*2)-nilaiSkrg))*0.5)*timb.netto/1000
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-nilaiSkrg)*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,2)*2)-nilaiSkrg))*0.5)*timb.netto/1000
                end
			end
            ) as nilaiBulan,
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-nilaiSkrg)*0.5)
                else (12-(((left(s_field.periode,1)*2)-nilaiSkrg))*0.5)
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-nilaiSkrg)*0.5)
                else (12-(((left(s_field.periode,2)*2)-nilaiSkrg))*0.5)
                end
			end
            ) as umurBulan,
            (timb.netto/1000) as jmlNetto,
            spta.kode_kat_lahan
		from t_timbangan timb
		inner join t_spta spta on spta.id = timb.id_spat
		inner join sap_field s_field on s_field.kode_blok = spta.kode_blok
		where spta.tgl_spta >= p_TglTebu and spta.tgl_spta <= p_Tglebu2
	) as total;

END$$

DROP PROCEDURE IF EXISTS `umur_tebu_cb`$$
CREATE DEFINER=`root`@`%` PROCEDURE `umur_tebu_cb` ()  BEGIN
    declare nilaiSkrg int;
    
    if (day(CURDATE()) < 16)
    then select(month(curdate())*2)-1 into nilaiSkrg;
    else select(month(curdate())*2) into nilaiSkrg;
    end if;
    
    select
		cast(sum(case when umurBulan < 10 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_a,
        cast(sum(case when (umurBulan < 11 && umurBulan >= 10) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_b,
        cast(sum(case when (umurBulan < 12 && umurBulan >= 11) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_c,
        cast(sum(case when umurBulan >= 12 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_d,
        cast(sum(nilaiBulan)/sum(jmlNetto) as decimal(4,2)) as rataRata,
        cast(sum(jmlNetto) as decimal(8,2)) as tebumasuk
    from
    (
		select
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,1)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)*timb.netto/1000
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,2)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)*timb.netto/1000
                end
			end
            ) as nilaiBulan,
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)
                else (12-(((left(s_field.periode,1)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)
                else (12-(((left(s_field.periode,2)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)
                end
			end
            ) as umurBulan,
            (timb.netto/1000) as jmlNetto,
            spta.tgl_spta as tgl
		from t_timbangan timb
		inner join t_spta spta on spta.id = timb.id_spat
		inner join sap_field s_field on s_field.kode_blok = spta.kode_blok
		where spta.tgl_spta >= '2018-05-01'
	) as total;

END$$

DROP PROCEDURE IF EXISTS `umur_tebu_sd`$$
CREATE DEFINER=`root`@`%` PROCEDURE `umur_tebu_sd` (`tgl1` DATE, `tgl2` DATE)  BEGIN
    drop table if exists tempUmur;
    create table tempUmur as
    select
		(case when kode_kat_lahan = 'TS-HG' then 'TS' else case when kode_kat_lahan = 'TR-KR' then 'TR' else 'TSI' end end) as tstr,
		cast(sum(case when umurBulan < 10 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_a,
        cast(sum(case when (umurBulan < 11 && umurBulan >= 10) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_b,
        cast(sum(case when (umurBulan < 12 && umurBulan >= 11) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_c,
        cast(sum(case when umurBulan >= 12 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_d,
        cast(sum(nilaiBulan)/sum(jmlNetto) as decimal(4,2)) as rataRata,
        cast(sum(jmlNetto) as decimal(8,2)) as tebumasuk
    from
    (
		select
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,1)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)*timb.netto/1000
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,2)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)*timb.netto/1000
                end
			end
            ) as nilaiBulan,
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)
                else (12-(((left(s_field.periode,1)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)
                else (12-(((left(s_field.periode,2)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)
                end
			end
            ) as umurBulan,
            (timb.netto/1000) as jmlNetto,
            spta.tgl_spta as tgl,
            spta.kode_kat_lahan
		from t_timbangan timb
		inner join t_spta spta on spta.id = timb.id_spat
		inner join sap_field s_field on s_field.kode_blok = spta.kode_blok
		where spta.tgl_spta >= tgl1 and spta.tgl_spta <= tgl2
	) as total
    group by kode_kat_lahan
    union
    select
		'BUMA',
		cast(sum(case when umurBulan < 10 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_a,
        cast(sum(case when (umurBulan < 11 && umurBulan >= 10) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_b,
        cast(sum(case when (umurBulan < 12 && umurBulan >= 11) then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_c,
        cast(sum(case when umurBulan >= 12 then 1 else 0 end)/count(umurBulan)*100 as decimal(4,1)) as umur_d,
        cast(sum(nilaiBulan)/sum(jmlNetto) as decimal(4,2)) as rataRata,
        cast(sum(jmlNetto) as decimal(8,2)) as tebumasuk
    from
    (
		select
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,1)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)*timb.netto/1000
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)*timb.netto/1000
                else (12-(((left(s_field.periode,2)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)*timb.netto/1000
                end
			end
            ) as nilaiBulan,
            (
			case when (length(s_field.periode) = 2)
            then
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,1)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)
                else (12-(((left(s_field.periode,1)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)
                end
			else
				case when (right(s_field.periode,1) = 'A')
                then (12-(((left(s_field.periode,2)*2)-1)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2))*0.5)
                else (12-(((left(s_field.periode,2)*2)-if(day(spta.tgl_spta)<16,(month(spta.tgl_spta)*2)-1,month(spta.tgl_spta)*2)))*0.5)
                end
			end
            ) as umurBulan,
            (timb.netto/1000) as jmlNetto,
            spta.tgl_spta as tgl,
            spta.kode_kat_lahan
		from t_timbangan timb
		inner join t_spta spta on spta.id = timb.id_spat
		inner join sap_field s_field on s_field.kode_blok = spta.kode_blok
		where spta.tgl_spta >= tgl1 and spta.tgl_spta <= tgl2
	) as total;

END$$

DROP PROCEDURE IF EXISTS `update_ari`$$
CREATE DEFINER=`root`@`%` PROCEDURE `update_ari` ()  BEGIN
	declare vHablurAnalisa double(4,2);
	update t_ari ari
	join t_spta spta on spta.id = ari.id_spta
	join TBL_CORELAB cs on cs.NUMERATOR = spta.no_spat
	join t_timbangan timb on timb.id_spat = spta.id
    join tbl_faktor faktor on faktor.tgl_timbang = spta.tgl_timbang
	set 
		ari.persen_brix_ari = cs.BRIX, 
		ari.persen_pol_ari = cs.POL, 
        ari.HK = cs.HK, 
        ari.nilai_nira = cs.NNPP,
        ari.faktor_rendemen = cs.KNPP,
		ari.faktor_regresi = 0.0,
        ari.rendemen_ari = (cs.NNPP*cs.KNPP),
        ari.rendemen_individu =
			IF(cs.RAFAKSI = 0,
				IF((((cs.NNPP*cs.KNPP/100)*(faktor.faktor_efektif)*(timb.netto_final))/(timb.netto_final)*100) < 6, 6,
					(((cs.NNPP*cs.KNPP/100)*(faktor.faktor_efektif)*(timb.netto_final))/(timb.netto_final)*100)),
			((6/100)*(1-cs.RAFAKSI/100)*(timb.netto_final))/(timb.netto_final)*100),
		ari.hablur_ari = ROUND(ari.rendemen_individu/100*(timb.netto_final),0),
        ari.gula_total = ROUND((ari.hablur_ari * 1.003),0),
        ari.rendemen_ptr = ROUND((ari.gula_total/(timb.netto_final))*0.66*100,2),
        ari.gula_ptr = ROUND((ari.gula_total * 0.66),0),
        ari.tetes_ptr = ROUND((timb.netto_final)*0.03,0),
        ari.gula_pg = ROUND(ari.gula_total - ari.gula_ptr,0),
        ari.faktor_konversi = 0.0
	where cs.TSTR = 'TR';
END$$

DROP PROCEDURE IF EXISTS `update_hektar`$$
CREATE DEFINER=`root`@`%` PROCEDURE `update_hektar` (`pkode_blok` VARCHAR(10))  BEGIN
update t_selektor sel
	join t_spta spta on spta.id = sel.id_spta
    join temp_buka_validasi vald on vald.kode_blok = spta.kode_blok
    join sap_field fld on fld.kode_blok = spta.kode_blok
    join (
		select count(*) as total_rit from t_spta spta
			where spta.kode_blok = pkode_blok and spta.timb_netto_status = 1
    ) as total
    join (
		select sum(timb.netto_final) as ton_tebu from t_timbangan timb
			join t_spta spta on spta.id = timb.id_spat
		where spta.kode_blok = pkode_blok and spta.timb_netto_status = 1
    ) as total_tebu
set sel.tanaman_status = 1, sel.tanaman_user = 'Operator Tanaman', sel.tanaman_act = '2019-10-18 09:00', 
sel.ha_tertebang = ROUND(fld.luas_ha/total.total_rit,2),	fld.aff_tebang = 1, fld.luas_tebang = fld.luas_ha,
fld.total_tebang = total_tebu.ton_tebu
where vald.kode_blok = pkode_blok;

update t_selektor sel
	join t_spta spta on spta.id = sel.id_spta
    join temp_buka_validasi vald on vald.kode_blok = spta.kode_blok
    join sap_field fld on fld.kode_blok = spta.kode_blok
	join (
		select max(sel.id_selektor) as max_id, sel.ha_tertebang as max_ha from t_selektor sel
			join t_spta spta on spta.id = sel.id_spta
		where spta.kode_blok = pkode_blok
    ) as rit_akhir
    join (
		select sum(sel.ha_tertebang) as jml_ha from t_selektor sel
			join t_spta spta on spta.id = sel.id_spta
		where spta.kode_blok = pkode_blok and spta.timb_netto_status = 1
    ) as jml_ha
set sel.ha_tertebang = fld.luas_ha - (jml_ha.jml_ha - rit_akhir.max_ha)
where sel.id_selektor = rit_akhir.max_id;

END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `get_count_faktor`$$
CREATE DEFINER=`root`@`%` FUNCTION `get_count_faktor` (`pTgl` DATE) RETURNS INT(11) BEGIN
	RETURN (select count(*) from tbl_faktor where tgl_timbang = pTgl);
END$$

DROP FUNCTION IF EXISTS `get_hablur_ari`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_hablur_ari` (`netto` DOUBLE, `rendemen` DOUBLE(10,2)) RETURNS DOUBLE BEGIN



    declare hasil double;



    set hasil = netto*rendemen/100;



	return ROUND_UP(hasil,2);



    END$$

DROP FUNCTION IF EXISTS `get_hari_giling`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_hari_giling` () RETURNS INT(11) BEGIN



	declare hargil int;



	declare temptgl date;



	declare temphargil int;



	



	select ifnull(max(tgl_giling),get_tgl_giling()) into temptgl from t_spta;



	



	set temphargil = datediff(get_tgl_giling(),temptgl);



	if temphargil = 0 then



		set temphargil = 1;



	end if;



	



	SELECT IFNULL(MAX(hari_giling),(SELECT IFNULL(MAX(hari_giling),0) FROM t_spta)+temphargil) into hargil FROM t_spta WHERE tgl_giling=get_tgl_giling();



	return hargil;



    END$$

DROP FUNCTION IF EXISTS `get_kode_kat_lahan_ptp`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_kode_kat_lahan_ptp` (`_ket_sap` VARCHAR(10), `_jenis_tanah_sap` VARCHAR(10), `_status_blok_sap` VARCHAR(10)) RETURNS VARCHAR(10) CHARSET latin1 BEGIN


	#Routine body goes here...


	DECLARE _kode_kat_ptp VARCHAR(10);


SELECT 


    a.kode_kat_ptp INTO _kode_kat_ptp


  FROM


    m_kat_lahan_ptp as a


  WHERE kat_sap = _ket_sap 


    AND `jenis_tanah_sap` LIKE CONCAT('%', TRIM(IFNULL(_jenis_tanah_sap, '')), '%') 


    AND `status_blok_sap` LIKE CONCAT('%', TRIM(IFNULL(_status_blok_sap, '')), '%');


	RETURN _kode_kat_ptp;


END$$

DROP FUNCTION IF EXISTS `get_rendemen_bagihasil_ptr`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_rendemen_bagihasil_ptr` (`vkat` VARCHAR(2), `vrendemen` DOUBLE(10,2)) RETURNS DOUBLE(10,4) BEGIN



	/* jika rendemen < = 6 bagihasil = 66 % */



	/* jika rendemen >  6 bagihasil = 66 % dan < = 7.99 => 66% + (selisih dari 6 * 70%)*/



	/* jika rendemen >=  8 bagihasil =  66% + (selisih dari 6 * 75%)*/



	declare hslrendemen double(10,4);



	DECLARE temphslrendemen1 DOUBLE(10,4);



	DECLARE temphslrendemen2 DOUBLE(10,4);



	DECLARE temphslrendemen3 DOUBLE(10,4);



	declare selisih double(10,2);



	



	



	if vkat = 'TR' then



	if vrendemen <= 6 then



		set hslrendemen = vrendemen * 66 / 100;



	elseif vrendemen > 6 and vrendemen <= 8 then



		set temphslrendemen1  = 6.00 * 66 / 100;



		set selisih = vrendemen - 6.00;



		SET temphslrendemen2  = selisih * 70 / 100;



		set hslrendemen = temphslrendemen1+temphslrendemen2;



	elseif vrendemen > 8 then



		SET temphslrendemen1  = 6.00 * 66 / 100;



		set temphslrendemen2 = 2.00 * 70 / 100;



		SET selisih = vrendemen - 8.00;



		SET temphslrendemen3  = selisih * 75 / 100;



		SET hslrendemen = temphslrendemen1+temphslrendemen2+temphslrendemen3;



	end if;



	else



	 set hslrendemen = 0;



	end if;



	



	return hslrendemen;



    END$$

DROP FUNCTION IF EXISTS `get_tgl_giling`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_tgl_giling` () RETURNS DATE BEGIN
	DECLARE tgl date;
	SELECT IF(STR_TO_DATE(now(),'%Y-%m-%d %H:%i:%s') < STR_TO_DATE(CONCAT(DATE(now()),' 05:59:59'),'%Y-%m-%d %H:%i:%s'),
STR_TO_DATE(NOW(),'%Y-%m-%d') - INTERVAL 1 DAY, STR_TO_DATE(NOW(),'%Y-%m-%d')) into tgl;
	return tgl;
    END$$

DROP FUNCTION IF EXISTS `NewProc`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `NewProc` (`_ket_sap` VARCHAR(10), `_jenis_tanah_sap` VARCHAR(10), `_status_blok_sap` VARCHAR(10)) RETURNS INT(11) BEGIN


	#Routine body goes here...


	DECLARE _kode_kat_ptp VARCHAR(10);


SELECT 


    a.kode_kat_ptp INTO _kode_kat_ptp


  FROM


    m_kat_lahan_ptp as a


  WHERE kat_sap = _ket_sap 


    AND `jenis_tanah_sap` LIKE CONCAT(TRIM(IFNULL(_jenis_tanah_sap, '')), '%') 


    AND `status_blok_sap` LIKE CONCAT(TRIM(IFNULL(_status_blok_sap, '')), '%');


	RETURN _kode_kat_ptp;


END$$

DROP FUNCTION IF EXISTS `ROUND_UP`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `ROUND_UP` (`num` DECIMAL(32,16), `places` INT) RETURNS DECIMAL(32,2) RETURN CASE WHEN num < 0



THEN - ceil(abs(num) * power(10, places)) / power(10, places)



ELSE ceil(abs(num) * power(10, places)) / power(10, places)



END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
