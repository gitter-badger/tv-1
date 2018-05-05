/*
Date: 2017-11-28 12:47:02
*/
-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2018-03-13 03:57:58
-- 服务器版本： 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `rhaphp`
--

-- --------------------------------------------------------

--
-- 表的结构 `rh_mp_vip`
--

DROP TABLE IF EXISTS `rh_mp_vip`;
CREATE TABLE IF NOT EXISTS `rh_mp_vip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL COMMENT '公众号id',
  `fid` int(11) NOT NULL COMMENT '会员id',
  `birthday` date DEFAULT NULL COMMENT '出生年月',
  `card_number` varchar(18) DEFAULT NULL COMMENT '身份证号码',
  `invite_number` int(6) NOT NULL COMMENT '邀请码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='会员表';

--
-- 表的结构 `rh_weshop_banner`
--

DROP TABLE IF EXISTS `rh_weshop_banner`;
CREATE TABLE IF NOT EXISTS `rh_weshop_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL COMMENT '公众号ID',
  `sort` int(11) NOT NULL COMMENT '排序',
  `images` varchar(150) NOT NULL COMMENT '图片',
  `url` varchar(150) DEFAULT NULL COMMENT '链接',
  `status` int(1) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='商城首页banner';

--
-- 表的结构 `rh_weshop_goods`
--

DROP TABLE IF EXISTS `rh_weshop_goods`;
CREATE TABLE IF NOT EXISTS `rh_weshop_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL COMMENT '公众号ID',
  `type` int(6) NOT NULL COMMENT '分类ID',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `images` text NOT NULL COMMENT '图片',
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `freight` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `content` longtext NOT NULL COMMENT '详情',
  `count` int(6) NOT NULL COMMENT '库存数量',
  `status` int(1) NOT NULL COMMENT '状态',
  `index` int(1) NOT NULL DEFAULT '0' COMMENT '是否首页显示',
  `sale` int(11) NOT NULL DEFAULT '0' COMMENT '已出售数量（交易成功累计增加）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='商品表';

--
-- 表的结构 `rh_weshop_type`
--

DROP TABLE IF EXISTS `rh_weshop_type`;
CREATE TABLE IF NOT EXISTS `rh_weshop_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL COMMENT '公众号ID',
  `sort` int(11) NOT NULL COMMENT '排序',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `icon` varchar(100) NOT NULL COMMENT '分类icon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='商城分类表';

--
-- 表的结构 `rh_weshop_order`
--

DROP TABLE IF EXISTS `rh_weshop_order`;
CREATE TABLE IF NOT EXISTS `rh_weshop_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL COMMENT 'openid',
  `mpid` int(11) NOT NULL COMMENT '公众号ID',
  `goods_id` text NOT NULL COMMENT '商品id合集',
  `payment_id` int(11) NOT NULL DEFAULT '0' COMMENT '微信支付账单对应id',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态（0:待付款；1:已付款）',
  `address_id` int(11) NOT NULL COMMENT '用户收货地址',
  `time` int(10) NOT NULL COMMENT '下单时间',
  `remark` text COMMENT '备注',
  `company` varchar(100) DEFAULT NULL COMMENT '快递公司id',
  `logistics_num` varchar(200) DEFAULT NULL COMMENT '物流单号',
  `is_send` int(1) DEFAULT '0' COMMENT '是否发货',
  `is_finish` int(1) DEFAULT '0' COMMENT '是否确认收货',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='订单表';

--
-- 表的结构 `rh_weshop_car`
--

DROP TABLE IF EXISTS `rh_weshop_car`;
CREATE TABLE IF NOT EXISTS `rh_weshop_car` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL COMMENT '公众号ID',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `count` int(11) NOT NULL COMMENT '数量',
  `openid` varchar(255) NOT NULL COMMENT '用户openid',
  `time` int(10) NOT NULL COMMENT '添加购物车的时间',
  `status` int(1) DEFAULT '0' COMMENT '状态0未付款1已付款',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='购物车表';

--
-- 表的结构 `rh_weshop_address`
--

DROP TABLE IF EXISTS `rh_weshop_address`;
CREATE TABLE IF NOT EXISTS `rh_weshop_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL COMMENT '公众号ID',
  `openid` varchar(255) NOT NULL COMMENT '微信openid',
  `province` varchar(30) NOT NULL COMMENT '省份',
  `city` varchar(30) NOT NULL COMMENT '城市',
  `area` varchar(100) NOT NULL COMMENT '区/县',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `name` varchar(30) NOT NULL COMMENT '收件人姓名',
  `mobile` varchar(15) NOT NULL COMMENT '收件人手机号',
  `time` int(10) NOT NULL COMMENT '添加的时间',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认地址',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='商城收货地址表';

--
-- 表的结构 `rh_weshop_comment`
--

DROP TABLE IF EXISTS `rh_weshop_comment`;
CREATE TABLE IF NOT EXISTS `rh_weshop_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL,
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `content` text COMMENT '评论',
  `images` text COMMENT '图片',
  `time` int(10) DEFAULT NULL COMMENT '评论时间',
  `star1` int(1) DEFAULT NULL COMMENT '描述相符',
  `star2` int(1) DEFAULT NULL COMMENT '服务态度',
  `star3` int(1) DEFAULT NULL COMMENT '发货速度',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='商城商品评价表';
