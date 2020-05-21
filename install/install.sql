-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-04-25 13:16:35
-- 服务器版本： 5.7.26
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `faka`
--

-- --------------------------------------------------------

--
-- 表的结构 `bf_admin`
--

CREATE TABLE `bf_admin` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_admin`
--

INSERT INTO `bf_admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'f6eaa20e3d6599c031e59b1f20d13a81');

-- --------------------------------------------------------

--
-- 表的结构 `bf_config`
--

CREATE TABLE `bf_config` (
  `id` int(11) NOT NULL,
  `gg` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '公告',
  `name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '网站名字',
  `upkey` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `rec` int(11) NOT NULL COMMENT '首页推荐数量',
  `kf` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '客服',
  `monit` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '邮件key',
  `copy` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '网站底部',
  `link` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '友情链接',
  `emailfk` int(11) NOT NULL DEFAULT '0',
  `stockno` int(11) NOT NULL DEFAULT '0' COMMENT '库存显示',
  `querygg` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '查询页公告',
  `early` int(11) NOT NULL DEFAULT '0',
  `earlylim` int(11) NOT NULL DEFAULT '0',
  `sold` int(11) NOT NULL DEFAULT '0',
  `pcgg` text,
  `template` int(11) NOT NULL DEFAULT '1',
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_config`
--

INSERT INTO `bf_config` (`id`, `gg`, `name`, `upkey`, `rec`, `kf`, `monit`, `copy`, `link`, `emailfk`, `stockno`, `querygg`, `early`, `earlylim`, `sold`, `pcgg`, `template`, `logo`) VALUES
(1, '<p style=\"color:red\">欢迎使用BF发卡系统</p>', 'BF发卡网', '76978050', 4, '123456', '77058492', '版权所有 BF发卡网 ', '<a href=\"https://osuu.net\">捕风阁</a>', 0, 2, '请输入联系方式或商户单号查询，交易单号无法查询', 0, 3, 0, '测试测试测试测试测试测试测试测试', 1, '1587790283.png');

-- --------------------------------------------------------

--
-- 表的结构 `bf_email`
--

CREATE TABLE `bf_email` (
  `id` int(11) NOT NULL,
  `mail_stmp` text,
  `mail_port` text,
  `mail_usr` text,
  `mail_pwd` text,
  `mail_rece` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_email`
--

INSERT INTO `bf_email` (`id`, `mail_stmp`, `mail_port`, `mail_usr`, `mail_pwd`, `mail_rece`) VALUES
(1, '', '465', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `bf_goods`
--

CREATE TABLE `bf_goods` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `info` longtext,
  `img` varchar(110) DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `price` float DEFAULT NULL,
  `minbuy` int(11) DEFAULT NULL,
  `maxbuy` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT '1',
  `weight` int(4) DEFAULT '1',
  `sales` int(11) DEFAULT '0',
  `time` int(11) NOT NULL,
  `sm1` text,
  `sm2` text,
  `sm3` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_goods`
--

INSERT INTO `bf_goods` (`id`, `name`, `info`, `img`, `type_id`, `price`, `minbuy`, `maxbuy`, `state`, `weight`, `sales`, `time`, `sm1`, `sm2`, `sm3`) VALUES
(16, 'BF_TEST_GOODS2', '', '1587790736.png', 15, 5, 1, 9999, 1, 0, 0, 1587704463, '官方正品', '', ''),
(17, 'BF_TEST_GOODS3', '', '1587790750.png', 16, 5, 1, 9999, 1, 0, 0, 1587704475, '', '', ''),
(18, 'BF_TEST_GOODS4', '', '1587790783.png', 13, 5, 1, 9999, 1, 0, 0, 1587704490, '', '', ''),
(19, 'BF_TEST_GOODS5', '', '1587790834.png', 15, 5, 1, 9999, 1, 0, 0, 1587704508, '', '', ''),
(13, 'BF_TEST_GOODS1', '', '1587790718.png', 13, 6, 1, 10, 1, 13, 0, 1587650592, '官方正品', '自动发货', '7天无理由');

-- --------------------------------------------------------

--
-- 表的结构 `bf_km`
--

CREATE TABLE `bf_km` (
  `id` int(11) NOT NULL,
  `goodsid` int(11) NOT NULL,
  `km` text,
  `starttime` int(11) DEFAULT NULL,
  `endtime` int(11) DEFAULT NULL,
  `out_trade_no` varchar(100) DEFAULT NULL,
  `trade_no` varchar(100) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `gName` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `bf_order`
--

CREATE TABLE `bf_order` (
  `id` int(11) NOT NULL,
  `out_trade_no` varchar(100) DEFAULT NULL,
  `trade_no` varchar(100) DEFAULT NULL,
  `goodsid` int(11) DEFAULT NULL,
  `money` float DEFAULT NULL,
  `user` varchar(30) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `startTime` int(11) DEFAULT NULL,
  `endTime` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `gName` text,
  `flag` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `bf_pay`
--

CREATE TABLE `bf_pay` (
  `id` int(11) NOT NULL,
  `alipay` int(11) NOT NULL DEFAULT '0',
  `wxpay` int(11) NOT NULL DEFAULT '0',
  `qqpay` int(11) NOT NULL DEFAULT '0',
  `epayurl` text,
  `epayid` text,
  `epaykey` text,
  `codeid` text,
  `codekey` text,
  `ali_partner` text,
  `ali_seller` text,
  `ali_key` text,
  `f2f_appid` text,
  `f2f_public` text,
  `f2f_private` text,
  `wx_appid` text,
  `wx_mchid` text,
  `wx_key` text,
  `wx_secret` text,
  `qq_mchid` text,
  `qq_mchkey` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_pay`
--

INSERT INTO `bf_pay` (`id`, `alipay`, `wxpay`, `qqpay`, `epayurl`, `epayid`, `epaykey`, `codeid`, `codekey`, `ali_partner`, `ali_seller`, `ali_key`, `f2f_appid`, `f2f_public`, `f2f_private`, `wx_appid`, `wx_mchid`, `wx_key`, `wx_secret`, `qq_mchid`, `qq_mchkey`) VALUES
(1, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `bf_promotion`
--

CREATE TABLE `bf_promotion` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `endtime` int(11) NOT NULL,
  `goodsid` int(11) DEFAULT NULL,
  `discount` int(11) NOT NULL,
  `gName` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_promotion`
--

INSERT INTO `bf_promotion` (`id`, `name`, `endtime`, `goodsid`, `discount`, `gName`) VALUES
(29, '新品', 1588780800, 13, 4, 'BF_TEST_GOODS1');

-- --------------------------------------------------------

--
-- 表的结构 `bf_swiper`
--

CREATE TABLE `bf_swiper` (
  `id` int(11) NOT NULL,
  `img` text NOT NULL,
  `url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_swiper`
--

INSERT INTO `bf_swiper` (`id`, `img`, `url`) VALUES
(1, 'upload/1.JPG', 'http://osuu.net'),
(3, 'upload/2.JPG', 'http://mall.osuu.net'),
(6, 'upload/3.JPG', 'http://osuu.net');

-- --------------------------------------------------------

--
-- 表的结构 `bf_type`
--

CREATE TABLE `bf_type` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `starttime` int(11) NOT NULL,
  `weight` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `bf_type`
--

INSERT INTO `bf_type` (`id`, `name`, `starttime`, `weight`, `status`) VALUES
(13, 'BF_TEST_1', 1586837368, 9, 1),
(15, 'BF_TEST_2', 1587790623, 5, 1),
(16, 'BF_TEST_3', 1587790631, 5, 1);

--
-- 转储表的索引
--

--
-- 表的索引 `bf_admin`
--
ALTER TABLE `bf_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- 表的索引 `bf_config`
--
ALTER TABLE `bf_config`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bf_email`
--
ALTER TABLE `bf_email`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bf_goods`
--
ALTER TABLE `bf_goods`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bf_km`
--
ALTER TABLE `bf_km`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bf_order`
--
ALTER TABLE `bf_order`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bf_pay`
--
ALTER TABLE `bf_pay`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bf_promotion`
--
ALTER TABLE `bf_promotion`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bf_swiper`
--
ALTER TABLE `bf_swiper`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- 表的索引 `bf_type`
--
ALTER TABLE `bf_type`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `bf_config`
--
ALTER TABLE `bf_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- 使用表AUTO_INCREMENT `bf_email`
--
ALTER TABLE `bf_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `bf_goods`
--
ALTER TABLE `bf_goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用表AUTO_INCREMENT `bf_km`
--
ALTER TABLE `bf_km`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- 使用表AUTO_INCREMENT `bf_order`
--
ALTER TABLE `bf_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- 使用表AUTO_INCREMENT `bf_pay`
--
ALTER TABLE `bf_pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `bf_promotion`
--
ALTER TABLE `bf_promotion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- 使用表AUTO_INCREMENT `bf_swiper`
--
ALTER TABLE `bf_swiper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `bf_type`
--
ALTER TABLE `bf_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
