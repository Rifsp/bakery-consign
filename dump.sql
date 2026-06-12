--
-- PostgreSQL database dump
--

-- Dumped from database version 12.14
-- Dumped by pg_dump version 12.14

-- Started on 2026-06-12 14:22:47

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 1494051)
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- TOC entry 3367 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- TOC entry 703 (class 1247 OID 1494100)
-- Name: jenis_mutasi; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.jenis_mutasi AS ENUM (
    'masuk',
    'keluar',
    'retur_masuk',
    'retur_keluar',
    'penyesuaian',
    'kirim_ke_sales',
    'retur_dari_sales'
);


ALTER TYPE public.jenis_mutasi OWNER TO postgres;

--
-- TOC entry 825 (class 1247 OID 1494676)
-- Name: jenis_mutasi_sales; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.jenis_mutasi_sales AS ENUM (
    'masuk_dari_gudang',
    'keluar_ke_toko',
    'retur_dari_toko',
    'penyesuaian'
);


ALTER TYPE public.jenis_mutasi_sales OWNER TO postgres;

--
-- TOC entry 694 (class 1247 OID 1494076)
-- Name: status_kunjungan; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.status_kunjungan AS ENUM (
    'pending',
    'selesai'
);


ALTER TYPE public.status_kunjungan OWNER TO postgres;

--
-- TOC entry 700 (class 1247 OID 1494090)
-- Name: status_pembelian; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.status_pembelian AS ENUM (
    'pending',
    'diterima',
    'sebagian',
    'dibatalkan'
);


ALTER TYPE public.status_pembelian OWNER TO postgres;

--
-- TOC entry 606 (class 1247 OID 1494068)
-- Name: status_penitipan; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.status_penitipan AS ENUM (
    'aktif',
    'selesai',
    'ditarik'
);


ALTER TYPE public.status_penitipan OWNER TO postgres;

--
-- TOC entry 697 (class 1247 OID 1494082)
-- Name: status_retur; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.status_retur AS ENUM (
    'pending',
    'disetujui',
    'ditolak'
);


ALTER TYPE public.status_retur OWNER TO postgres;

--
-- TOC entry 603 (class 1247 OID 1494063)
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.user_role AS ENUM (
    'admin',
    'sales'
);


ALTER TYPE public.user_role OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 259 (class 1259 OID 1499865)
-- Name: ci_sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ci_sessions (
    id character varying(128) NOT NULL,
    ip_address character varying(45) NOT NULL,
    "timestamp" timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    data bytea DEFAULT '\x'::bytea NOT NULL
);


ALTER TABLE public.ci_sessions OWNER TO postgres;

--
-- TOC entry 212 (class 1259 OID 1494184)
-- Name: harga_jual; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.harga_jual (
    id integer NOT NULL,
    produk_id integer NOT NULL,
    nama_harga character varying(50) NOT NULL,
    harga numeric(12,2) NOT NULL,
    fee_sales numeric(12,2) DEFAULT 0 NOT NULL,
    is_aktif boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.harga_jual OWNER TO postgres;

--
-- TOC entry 211 (class 1259 OID 1494182)
-- Name: harga_jual_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.harga_jual_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.harga_jual_id_seq OWNER TO postgres;

--
-- TOC entry 3368 (class 0 OID 0)
-- Dependencies: 211
-- Name: harga_jual_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.harga_jual_id_seq OWNED BY public.harga_jual.id;


--
-- TOC entry 208 (class 1259 OID 1494148)
-- Name: kategori_produk; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_produk (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    deskripsi text,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.kategori_produk OWNER TO postgres;

--
-- TOC entry 207 (class 1259 OID 1494146)
-- Name: kategori_produk_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kategori_produk_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kategori_produk_id_seq OWNER TO postgres;

--
-- TOC entry 3369 (class 0 OID 0)
-- Dependencies: 207
-- Name: kategori_produk_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kategori_produk_id_seq OWNED BY public.kategori_produk.id;


--
-- TOC entry 230 (class 1259 OID 1494388)
-- Name: kunjungan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kunjungan (
    id integer NOT NULL,
    nomor_kunjungan character varying(30) NOT NULL,
    toko_id integer NOT NULL,
    sales_id integer NOT NULL,
    tanggal date DEFAULT CURRENT_DATE NOT NULL,
    status public.status_kunjungan DEFAULT 'pending'::public.status_kunjungan NOT NULL,
    catatan text,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.kunjungan OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 1494386)
-- Name: kunjungan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kunjungan_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kunjungan_id_seq OWNER TO postgres;

--
-- TOC entry 3370 (class 0 OID 0)
-- Dependencies: 229
-- Name: kunjungan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kunjungan_id_seq OWNED BY public.kunjungan.id;


--
-- TOC entry 258 (class 1259 OID 1494736)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id bigint NOT NULL,
    version character varying(255) NOT NULL,
    class character varying(255) NOT NULL,
    "group" character varying(255) NOT NULL,
    namespace character varying(255) NOT NULL,
    "time" integer NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 257 (class 1259 OID 1494734)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 3371 (class 0 OID 0)
-- Dependencies: 257
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 222 (class 1259 OID 1494291)
-- Name: mutasi_gudang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mutasi_gudang (
    id integer NOT NULL,
    produk_id integer NOT NULL,
    jenis public.jenis_mutasi NOT NULL,
    jumlah integer NOT NULL,
    referensi_id integer,
    referensi_tabel character varying(50),
    keterangan text,
    dibuat_oleh integer,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.mutasi_gudang OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 1494289)
-- Name: mutasi_gudang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mutasi_gudang_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.mutasi_gudang_id_seq OWNER TO postgres;

--
-- TOC entry 3372 (class 0 OID 0)
-- Dependencies: 221
-- Name: mutasi_gudang_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mutasi_gudang_id_seq OWNED BY public.mutasi_gudang.id;


--
-- TOC entry 254 (class 1259 OID 1494687)
-- Name: mutasi_sales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mutasi_sales (
    id integer NOT NULL,
    sales_id integer NOT NULL,
    produk_id integer NOT NULL,
    jenis public.jenis_mutasi_sales NOT NULL,
    jumlah integer NOT NULL,
    referensi_id integer,
    referensi_tabel character varying(50),
    keterangan text,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.mutasi_sales OWNER TO postgres;

--
-- TOC entry 253 (class 1259 OID 1494685)
-- Name: mutasi_sales_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mutasi_sales_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.mutasi_sales_id_seq OWNER TO postgres;

--
-- TOC entry 3373 (class 0 OID 0)
-- Dependencies: 253
-- Name: mutasi_sales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mutasi_sales_id_seq OWNED BY public.mutasi_sales.id;


--
-- TOC entry 216 (class 1259 OID 1494224)
-- Name: pembelian; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pembelian (
    id integer NOT NULL,
    nomor_po character varying(30) NOT NULL,
    supplier_id integer NOT NULL,
    tanggal_pesan date DEFAULT CURRENT_DATE NOT NULL,
    tanggal_terima date,
    status public.status_pembelian DEFAULT 'pending'::public.status_pembelian NOT NULL,
    catatan text,
    total_nilai numeric(14,2) DEFAULT 0,
    dibuat_oleh integer NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.pembelian OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 1494252)
-- Name: pembelian_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pembelian_detail (
    id integer NOT NULL,
    pembelian_id integer NOT NULL,
    produk_id integer NOT NULL,
    jumlah_pesan integer DEFAULT 0 NOT NULL,
    jumlah_terima integer DEFAULT 0 NOT NULL,
    harga_beli numeric(12,2) NOT NULL,
    tgl_expired date,
    subtotal numeric(14,2) GENERATED ALWAYS AS (((jumlah_terima)::numeric * harga_beli)) STORED,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.pembelian_detail OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 1494250)
-- Name: pembelian_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pembelian_detail_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pembelian_detail_id_seq OWNER TO postgres;

--
-- TOC entry 3374 (class 0 OID 0)
-- Dependencies: 217
-- Name: pembelian_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pembelian_detail_id_seq OWNED BY public.pembelian_detail.id;


--
-- TOC entry 215 (class 1259 OID 1494222)
-- Name: pembelian_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pembelian_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pembelian_id_seq OWNER TO postgres;

--
-- TOC entry 3375 (class 0 OID 0)
-- Dependencies: 215
-- Name: pembelian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pembelian_id_seq OWNED BY public.pembelian.id;


--
-- TOC entry 250 (class 1259 OID 1494627)
-- Name: pengiriman_ke_sales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengiriman_ke_sales (
    id integer NOT NULL,
    nomor_kirim character varying(30) NOT NULL,
    sales_id integer NOT NULL,
    tanggal_kirim date DEFAULT CURRENT_DATE NOT NULL,
    catatan text,
    dibuat_oleh integer NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.pengiriman_ke_sales OWNER TO postgres;

--
-- TOC entry 252 (class 1259 OID 1494653)
-- Name: pengiriman_ke_sales_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengiriman_ke_sales_detail (
    id integer NOT NULL,
    pengiriman_ke_sales_id integer NOT NULL,
    produk_id integer NOT NULL,
    jumlah integer DEFAULT 0 NOT NULL,
    tgl_expired date NOT NULL,
    harga_beli numeric(12,2) DEFAULT 0 NOT NULL,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.pengiriman_ke_sales_detail OWNER TO postgres;

--
-- TOC entry 251 (class 1259 OID 1494651)
-- Name: pengiriman_ke_sales_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengiriman_ke_sales_detail_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pengiriman_ke_sales_detail_id_seq OWNER TO postgres;

--
-- TOC entry 3376 (class 0 OID 0)
-- Dependencies: 251
-- Name: pengiriman_ke_sales_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengiriman_ke_sales_detail_id_seq OWNED BY public.pengiriman_ke_sales_detail.id;


--
-- TOC entry 249 (class 1259 OID 1494625)
-- Name: pengiriman_ke_sales_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengiriman_ke_sales_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pengiriman_ke_sales_id_seq OWNER TO postgres;

--
-- TOC entry 3377 (class 0 OID 0)
-- Dependencies: 249
-- Name: pengiriman_ke_sales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengiriman_ke_sales_id_seq OWNED BY public.pengiriman_ke_sales.id;


--
-- TOC entry 224 (class 1259 OID 1494313)
-- Name: penitipan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penitipan (
    id integer NOT NULL,
    nomor_titip character varying(30) NOT NULL,
    toko_id integer NOT NULL,
    sales_id integer NOT NULL,
    tanggal_titip date DEFAULT CURRENT_DATE NOT NULL,
    status public.status_penitipan DEFAULT 'aktif'::public.status_penitipan NOT NULL,
    catatan text,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.penitipan OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 1494340)
-- Name: penitipan_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penitipan_detail (
    id integer NOT NULL,
    penitipan_id integer NOT NULL,
    produk_id integer NOT NULL,
    harga_jual_id integer NOT NULL,
    jumlah_titip integer DEFAULT 0 NOT NULL,
    tgl_expired date NOT NULL,
    harga_satuan numeric(12,2) NOT NULL,
    fee_satuan numeric(12,2) DEFAULT 0 NOT NULL,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.penitipan_detail OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 1494338)
-- Name: penitipan_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penitipan_detail_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.penitipan_detail_id_seq OWNER TO postgres;

--
-- TOC entry 3378 (class 0 OID 0)
-- Dependencies: 225
-- Name: penitipan_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.penitipan_detail_id_seq OWNED BY public.penitipan_detail.id;


--
-- TOC entry 223 (class 1259 OID 1494311)
-- Name: penitipan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penitipan_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.penitipan_id_seq OWNER TO postgres;

--
-- TOC entry 3379 (class 0 OID 0)
-- Dependencies: 223
-- Name: penitipan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.penitipan_id_seq OWNED BY public.penitipan.id;


--
-- TOC entry 232 (class 1259 OID 1494415)
-- Name: penjualan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penjualan (
    id integer NOT NULL,
    nomor_jual character varying(30) NOT NULL,
    kunjungan_id integer NOT NULL,
    toko_id integer NOT NULL,
    sales_id integer NOT NULL,
    tanggal date DEFAULT CURRENT_DATE NOT NULL,
    total_harga numeric(14,2) DEFAULT 0 NOT NULL,
    total_fee numeric(14,2) DEFAULT 0 NOT NULL,
    catatan text,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.penjualan OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 1494447)
-- Name: penjualan_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penjualan_detail (
    id integer NOT NULL,
    penjualan_id integer NOT NULL,
    produk_id integer NOT NULL,
    harga_jual_id integer NOT NULL,
    jumlah_terjual integer DEFAULT 0 NOT NULL,
    harga_satuan numeric(12,2) NOT NULL,
    fee_satuan numeric(12,2) DEFAULT 0 NOT NULL,
    hpp_satuan numeric(12,2) DEFAULT 0 NOT NULL,
    subtotal_harga numeric(14,2) GENERATED ALWAYS AS (((jumlah_terjual)::numeric * harga_satuan)) STORED,
    subtotal_fee numeric(14,2) GENERATED ALWAYS AS (((jumlah_terjual)::numeric * fee_satuan)) STORED,
    subtotal_hpp numeric(14,2) GENERATED ALWAYS AS (((jumlah_terjual)::numeric * hpp_satuan)) STORED,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.penjualan_detail OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 1494445)
-- Name: penjualan_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penjualan_detail_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.penjualan_detail_id_seq OWNER TO postgres;

--
-- TOC entry 3380 (class 0 OID 0)
-- Dependencies: 233
-- Name: penjualan_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.penjualan_detail_id_seq OWNED BY public.penjualan_detail.id;


--
-- TOC entry 231 (class 1259 OID 1494413)
-- Name: penjualan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penjualan_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.penjualan_id_seq OWNER TO postgres;

--
-- TOC entry 3381 (class 0 OID 0)
-- Dependencies: 231
-- Name: penjualan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.penjualan_id_seq OWNED BY public.penjualan.id;


--
-- TOC entry 210 (class 1259 OID 1494160)
-- Name: produk; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.produk (
    id integer NOT NULL,
    kode_produk character varying(30) NOT NULL,
    nama character varying(100) NOT NULL,
    kategori_id integer,
    satuan character varying(20) DEFAULT 'pcs'::character varying NOT NULL,
    hpp numeric(12,2) DEFAULT 0 NOT NULL,
    shelf_life_hari integer DEFAULT 3 NOT NULL,
    deskripsi text,
    foto character varying(255),
    is_aktif boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.produk OWNER TO postgres;

--
-- TOC entry 209 (class 1259 OID 1494158)
-- Name: produk_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.produk_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.produk_id_seq OWNER TO postgres;

--
-- TOC entry 3382 (class 0 OID 0)
-- Dependencies: 209
-- Name: produk_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.produk_id_seq OWNED BY public.produk.id;


--
-- TOC entry 236 (class 1259 OID 1494477)
-- Name: retur; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.retur (
    id integer NOT NULL,
    nomor_retur character varying(30) NOT NULL,
    kunjungan_id integer NOT NULL,
    toko_id integer NOT NULL,
    sales_id integer NOT NULL,
    tanggal date DEFAULT CURRENT_DATE NOT NULL,
    status public.status_retur DEFAULT 'pending'::public.status_retur NOT NULL,
    alasan text,
    catatan text,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.retur OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 1494509)
-- Name: retur_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.retur_detail (
    id integer NOT NULL,
    retur_id integer NOT NULL,
    produk_id integer NOT NULL,
    jumlah_retur integer DEFAULT 0 NOT NULL,
    kondisi character varying(50) DEFAULT 'baik'::character varying,
    tgl_expired date,
    keterangan text,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.retur_detail OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 1494507)
-- Name: retur_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.retur_detail_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.retur_detail_id_seq OWNER TO postgres;

--
-- TOC entry 3383 (class 0 OID 0)
-- Dependencies: 237
-- Name: retur_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.retur_detail_id_seq OWNED BY public.retur_detail.id;


--
-- TOC entry 235 (class 1259 OID 1494475)
-- Name: retur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.retur_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.retur_id_seq OWNER TO postgres;

--
-- TOC entry 3384 (class 0 OID 0)
-- Dependencies: 235
-- Name: retur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.retur_id_seq OWNED BY public.retur.id;


--
-- TOC entry 240 (class 1259 OID 1494533)
-- Name: stok_expired_toko; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stok_expired_toko (
    id integer NOT NULL,
    toko_id integer NOT NULL,
    produk_id integer NOT NULL,
    penitipan_detail_id integer,
    jumlah integer DEFAULT 0 NOT NULL,
    tgl_expired date NOT NULL,
    is_diretur boolean DEFAULT false NOT NULL,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.stok_expired_toko OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 1494531)
-- Name: stok_expired_toko_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stok_expired_toko_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stok_expired_toko_id_seq OWNER TO postgres;

--
-- TOC entry 3385 (class 0 OID 0)
-- Dependencies: 239
-- Name: stok_expired_toko_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stok_expired_toko_id_seq OWNED BY public.stok_expired_toko.id;


--
-- TOC entry 220 (class 1259 OID 1494274)
-- Name: stok_gudang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stok_gudang (
    id integer NOT NULL,
    produk_id integer NOT NULL,
    stok_tersedia integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.stok_gudang OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 1494272)
-- Name: stok_gudang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stok_gudang_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stok_gudang_id_seq OWNER TO postgres;

--
-- TOC entry 3386 (class 0 OID 0)
-- Dependencies: 219
-- Name: stok_gudang_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stok_gudang_id_seq OWNED BY public.stok_gudang.id;


--
-- TOC entry 248 (class 1259 OID 1494605)
-- Name: stok_sales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stok_sales (
    id integer NOT NULL,
    sales_id integer NOT NULL,
    produk_id integer NOT NULL,
    stok_tersedia integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.stok_sales OWNER TO postgres;

--
-- TOC entry 247 (class 1259 OID 1494603)
-- Name: stok_sales_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stok_sales_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stok_sales_id_seq OWNER TO postgres;

--
-- TOC entry 3387 (class 0 OID 0)
-- Dependencies: 247
-- Name: stok_sales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stok_sales_id_seq OWNED BY public.stok_sales.id;


--
-- TOC entry 228 (class 1259 OID 1494366)
-- Name: stok_toko; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stok_toko (
    id integer NOT NULL,
    toko_id integer NOT NULL,
    produk_id integer NOT NULL,
    stok_tersedia integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.stok_toko OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 1494364)
-- Name: stok_toko_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stok_toko_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stok_toko_id_seq OWNER TO postgres;

--
-- TOC entry 3388 (class 0 OID 0)
-- Dependencies: 227
-- Name: stok_toko_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stok_toko_id_seq OWNED BY public.stok_toko.id;


--
-- TOC entry 206 (class 1259 OID 1494132)
-- Name: suppliers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.suppliers (
    id integer NOT NULL,
    kode_supplier character varying(20) NOT NULL,
    nama character varying(100) NOT NULL,
    alamat text,
    telepon character varying(20),
    email character varying(100),
    kontak_person character varying(100),
    is_aktif boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.suppliers OWNER TO postgres;

--
-- TOC entry 205 (class 1259 OID 1494130)
-- Name: suppliers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.suppliers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.suppliers_id_seq OWNER TO postgres;

--
-- TOC entry 3389 (class 0 OID 0)
-- Dependencies: 205
-- Name: suppliers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.suppliers_id_seq OWNED BY public.suppliers.id;


--
-- TOC entry 214 (class 1259 OID 1494203)
-- Name: toko; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.toko (
    id integer NOT NULL,
    kode_toko character varying(20) NOT NULL,
    nama character varying(100) NOT NULL,
    pemilik character varying(100),
    alamat text,
    kelurahan character varying(100),
    kecamatan character varying(100),
    kota character varying(100),
    telepon character varying(20),
    sales_id integer NOT NULL,
    is_aktif boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.toko OWNER TO postgres;

--
-- TOC entry 213 (class 1259 OID 1494201)
-- Name: toko_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.toko_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.toko_id_seq OWNER TO postgres;

--
-- TOC entry 3390 (class 0 OID 0)
-- Dependencies: 213
-- Name: toko_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.toko_id_seq OWNED BY public.toko.id;


--
-- TOC entry 204 (class 1259 OID 1494113)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    role public.user_role DEFAULT 'sales'::public.user_role NOT NULL,
    email character varying(100),
    telepon character varying(20),
    foto character varying(255),
    is_aktif boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 203 (class 1259 OID 1494111)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 3391 (class 0 OID 0)
-- Dependencies: 203
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 245 (class 1259 OID 1494593)
-- Name: v_expired_toko; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_expired_toko AS
 SELECT se.toko_id,
    t.nama AS nama_toko,
    p.nama AS nama_produk,
    se.jumlah,
    se.tgl_expired,
    se.is_diretur,
    (se.tgl_expired < CURRENT_DATE) AS sudah_expired
   FROM ((public.stok_expired_toko se
     JOIN public.toko t ON ((t.id = se.toko_id)))
     JOIN public.produk p ON ((p.id = se.produk_id)))
  ORDER BY se.tgl_expired;


ALTER TABLE public.v_expired_toko OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 1494579)
-- Name: v_laporan_fee_sales; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_laporan_fee_sales AS
 SELECT u.id AS sales_id,
    u.nama AS nama_sales,
    pj.tanggal,
    t.nama AS nama_toko,
    p.nama AS nama_produk,
    pd.jumlah_terjual,
    pd.harga_satuan,
    pd.fee_satuan,
    pd.subtotal_fee AS total_fee
   FROM ((((public.penjualan pj
     JOIN public.penjualan_detail pd ON ((pd.penjualan_id = pj.id)))
     JOIN public.toko t ON ((t.id = pj.toko_id)))
     JOIN public.users u ON ((u.id = pj.sales_id)))
     JOIN public.produk p ON ((p.id = pd.produk_id)));


ALTER TABLE public.v_laporan_fee_sales OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 1494574)
-- Name: v_laporan_laba; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_laporan_laba AS
 SELECT pj.id,
    pj.nomor_jual,
    pj.tanggal,
    t.nama AS nama_toko,
    u.nama AS nama_sales,
    p.nama AS nama_produk,
    pd.jumlah_terjual,
    pd.harga_satuan,
    pd.fee_satuan,
    pd.hpp_satuan,
    pd.subtotal_harga,
    pd.subtotal_fee,
    pd.subtotal_hpp,
    ((pd.subtotal_harga - pd.subtotal_fee) - pd.subtotal_hpp) AS laba_bersih
   FROM ((((public.penjualan pj
     JOIN public.penjualan_detail pd ON ((pd.penjualan_id = pj.id)))
     JOIN public.toko t ON ((t.id = pj.toko_id)))
     JOIN public.users u ON ((u.id = pj.sales_id)))
     JOIN public.produk p ON ((p.id = pd.produk_id)));


ALTER TABLE public.v_laporan_laba OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 1494598)
-- Name: v_laporan_pembelian; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_laporan_pembelian AS
 SELECT pb.nomor_po,
    pb.tanggal_pesan,
    pb.tanggal_terima,
    s.nama AS nama_supplier,
    p.nama AS nama_produk,
    pd.jumlah_pesan,
    pd.jumlah_terima,
    pd.harga_beli,
    pd.tgl_expired,
    pd.subtotal,
    pb.status
   FROM (((public.pembelian pb
     JOIN public.pembelian_detail pd ON ((pd.pembelian_id = pb.id)))
     JOIN public.suppliers s ON ((s.id = pb.supplier_id)))
     JOIN public.produk p ON ((p.id = pd.produk_id)));


ALTER TABLE public.v_laporan_pembelian OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 1494718)
-- Name: v_laporan_pengiriman_ke_sales; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_laporan_pengiriman_ke_sales AS
 SELECT pk.nomor_kirim,
    pk.tanggal_kirim,
    u.nama AS nama_sales,
    p.nama AS nama_produk,
    pkd.jumlah,
    pkd.tgl_expired,
    pkd.harga_beli,
    ((pkd.jumlah)::numeric * pkd.harga_beli) AS nilai_kirim,
    admin.nama AS dikirim_oleh
   FROM ((((public.pengiriman_ke_sales pk
     JOIN public.pengiriman_ke_sales_detail pkd ON ((pkd.pengiriman_ke_sales_id = pk.id)))
     JOIN public.users u ON ((u.id = pk.sales_id)))
     JOIN public.users admin ON ((admin.id = pk.dibuat_oleh)))
     JOIN public.produk p ON ((p.id = pkd.produk_id)));


ALTER TABLE public.v_laporan_pengiriman_ke_sales OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 1494589)
-- Name: v_stok_gudang; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_stok_gudang AS
 SELECT p.id AS produk_id,
    p.kode_produk,
    p.nama AS nama_produk,
    p.satuan,
    p.hpp,
    sg.stok_tersedia,
    sg.updated_at
   FROM (public.stok_gudang sg
     JOIN public.produk p ON ((p.id = sg.produk_id)));


ALTER TABLE public.v_stok_gudang OWNER TO postgres;

--
-- TOC entry 255 (class 1259 OID 1494713)
-- Name: v_stok_sales; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_stok_sales AS
 SELECT u.id AS sales_id,
    u.nama AS nama_sales,
    p.id AS produk_id,
    p.kode_produk,
    p.nama AS nama_produk,
    p.satuan,
    ss.stok_tersedia,
    ss.updated_at
   FROM ((public.stok_sales ss
     JOIN public.users u ON ((u.id = ss.sales_id)))
     JOIN public.produk p ON ((p.id = ss.produk_id)))
  WHERE (u.role = 'sales'::public.user_role);


ALTER TABLE public.v_stok_sales OWNER TO postgres;

--
-- TOC entry 243 (class 1259 OID 1494584)
-- Name: v_stok_toko; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_stok_toko AS
 SELECT t.id AS toko_id,
    t.nama AS nama_toko,
    t.kecamatan,
    u.nama AS nama_sales,
    p.id AS produk_id,
    p.nama AS nama_produk,
    p.satuan,
    st.stok_tersedia,
    st.updated_at
   FROM (((public.stok_toko st
     JOIN public.toko t ON ((t.id = st.toko_id)))
     JOIN public.produk p ON ((p.id = st.produk_id)))
     JOIN public.users u ON ((u.id = t.sales_id)));


ALTER TABLE public.v_stok_toko OWNER TO postgres;

--
-- TOC entry 2927 (class 2604 OID 1494187)
-- Name: harga_jual id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.harga_jual ALTER COLUMN id SET DEFAULT nextval('public.harga_jual_id_seq'::regclass);


--
-- TOC entry 2918 (class 2604 OID 1494151)
-- Name: kategori_produk id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_produk ALTER COLUMN id SET DEFAULT nextval('public.kategori_produk_id_seq'::regclass);


--
-- TOC entry 2964 (class 2604 OID 1494391)
-- Name: kunjungan id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kunjungan ALTER COLUMN id SET DEFAULT nextval('public.kunjungan_id_seq'::regclass);


--
-- TOC entry 3008 (class 2604 OID 1494739)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 2950 (class 2604 OID 1494294)
-- Name: mutasi_gudang id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_gudang ALTER COLUMN id SET DEFAULT nextval('public.mutasi_gudang_id_seq'::regclass);


--
-- TOC entry 3006 (class 2604 OID 1494690)
-- Name: mutasi_sales id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_sales ALTER COLUMN id SET DEFAULT nextval('public.mutasi_sales_id_seq'::regclass);


--
-- TOC entry 2936 (class 2604 OID 1494227)
-- Name: pembelian id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian ALTER COLUMN id SET DEFAULT nextval('public.pembelian_id_seq'::regclass);


--
-- TOC entry 2942 (class 2604 OID 1494255)
-- Name: pembelian_detail id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian_detail ALTER COLUMN id SET DEFAULT nextval('public.pembelian_detail_id_seq'::regclass);


--
-- TOC entry 2998 (class 2604 OID 1494630)
-- Name: pengiriman_ke_sales id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales ALTER COLUMN id SET DEFAULT nextval('public.pengiriman_ke_sales_id_seq'::regclass);


--
-- TOC entry 3002 (class 2604 OID 1494656)
-- Name: pengiriman_ke_sales_detail id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales_detail ALTER COLUMN id SET DEFAULT nextval('public.pengiriman_ke_sales_detail_id_seq'::regclass);


--
-- TOC entry 2952 (class 2604 OID 1494316)
-- Name: penitipan id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan ALTER COLUMN id SET DEFAULT nextval('public.penitipan_id_seq'::regclass);


--
-- TOC entry 2957 (class 2604 OID 1494343)
-- Name: penitipan_detail id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan_detail ALTER COLUMN id SET DEFAULT nextval('public.penitipan_detail_id_seq'::regclass);


--
-- TOC entry 2969 (class 2604 OID 1494418)
-- Name: penjualan id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan ALTER COLUMN id SET DEFAULT nextval('public.penjualan_id_seq'::regclass);


--
-- TOC entry 2974 (class 2604 OID 1494450)
-- Name: penjualan_detail id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan_detail ALTER COLUMN id SET DEFAULT nextval('public.penjualan_detail_id_seq'::regclass);


--
-- TOC entry 2920 (class 2604 OID 1494163)
-- Name: produk id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk ALTER COLUMN id SET DEFAULT nextval('public.produk_id_seq'::regclass);


--
-- TOC entry 2982 (class 2604 OID 1494480)
-- Name: retur id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur ALTER COLUMN id SET DEFAULT nextval('public.retur_id_seq'::regclass);


--
-- TOC entry 2987 (class 2604 OID 1494512)
-- Name: retur_detail id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur_detail ALTER COLUMN id SET DEFAULT nextval('public.retur_detail_id_seq'::regclass);


--
-- TOC entry 2991 (class 2604 OID 1494536)
-- Name: stok_expired_toko id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_expired_toko ALTER COLUMN id SET DEFAULT nextval('public.stok_expired_toko_id_seq'::regclass);


--
-- TOC entry 2947 (class 2604 OID 1494277)
-- Name: stok_gudang id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_gudang ALTER COLUMN id SET DEFAULT nextval('public.stok_gudang_id_seq'::regclass);


--
-- TOC entry 2995 (class 2604 OID 1494608)
-- Name: stok_sales id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_sales ALTER COLUMN id SET DEFAULT nextval('public.stok_sales_id_seq'::regclass);


--
-- TOC entry 2961 (class 2604 OID 1494369)
-- Name: stok_toko id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_toko ALTER COLUMN id SET DEFAULT nextval('public.stok_toko_id_seq'::regclass);


--
-- TOC entry 2914 (class 2604 OID 1494135)
-- Name: suppliers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers ALTER COLUMN id SET DEFAULT nextval('public.suppliers_id_seq'::regclass);


--
-- TOC entry 2932 (class 2604 OID 1494206)
-- Name: toko id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.toko ALTER COLUMN id SET DEFAULT nextval('public.toko_id_seq'::regclass);


--
-- TOC entry 2909 (class 2604 OID 1494116)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3361 (class 0 OID 1499865)
-- Dependencies: 259
-- Data for Name: ci_sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ci_sessions (id, ip_address, "timestamp", data) FROM stdin;
ci_session:vtmvirsh41t050n69dj2hqgu6a3vok6e	::1	2026-06-11 11:00:23.108158+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135303432333b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:7a51r7i1dnfuprvhkgjrau18pssm5lhs	127.0.0.1	2026-06-11 11:04:53.785903+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135303639333b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:l4k08h258c7me060rauqp2pph9j1tjld	127.0.0.1	2026-06-11 11:04:54.129525+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135303639343b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:1jpctpc7gtil6vosbajjeqt8tfvl50f0	127.0.0.1	2026-06-11 11:04:54.622143+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135303639343b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:5umkavhj0k17113v580seg420u8bhptp	192.168.100.106	2026-06-11 11:08:52.320299+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135303933323b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:lodnr5lhgd37j5e90iqg23134n0obn7p	192.168.100.22	2026-06-11 11:36:06.998398+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135323536363b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f70656d62656c69616e223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:11ng3q6g4ua3dv3rs4idkj29mlbqebmb	192.168.100.22	2026-06-11 11:46:40.32485+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135333230303b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6b756e6a756e67616e223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:rn7uqkldanhpmtpc2v9uq709p904vfk5	192.168.100.22	2026-06-11 11:53:22.834153+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135333630323b5f63695f70726576696f75735f75726c7c733a35363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c61706f72616e2f73746f6b2d677564616e67223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:j4kcrdlib6lmvtor3v8a9ruue0jp2upd	192.168.100.22	2026-06-11 11:58:28.888609+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135333633343b5f63695f70726576696f75735f75726c7c733a35363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c61706f72616e2f73746f6b2d677564616e67223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:v9jmgvqoroul5potlan572rddv62sa6d	127.0.0.1	2026-06-11 11:59:58.372389+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135333939383b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:u0c4se9rovusi2g2gb5mngs86ugoaq5k	127.0.0.1	2026-06-11 11:59:58.478615+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135333939383b
ci_session:r15f1lk8kderpqce1co9gobffnfg7ogs	192.168.100.22	2026-06-11 11:59:58.57151+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135333939383b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:uevdragc8loi2a1aib4svsmi3fc28bba	127.0.0.1	2026-06-11 11:59:58.781949+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313135333939383b
ci_session:ap6op4q1ujnq2rlj01u3g46slchrl2lr	192.168.100.22	2026-06-11 13:56:39.833639+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136303939393b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:17dfcinnvnfh6os8glfvjpu17sk5amc1	127.0.0.1	2026-06-11 14:02:24.574698+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313334343b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:5kjr9s203167trhuv8p61r38725vupo7	127.0.0.1	2026-06-11 14:02:24.873602+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313334343b
ci_session:lpq00e6kqrrt4vce012735thgtmqnele	192.168.100.22	2026-06-11 14:02:25.004166+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313334343b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:m2k4hgkdjf50beu2654jl4ms4lqmdmt8	127.0.0.1	2026-06-11 14:05:55.582928+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313535353b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:de6dangkgcehg71jcopnta1qpobokaef	127.0.0.1	2026-06-11 14:05:55.802887+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313535353b
ci_session:ukgmtbgasdk7sdvgbe146hjgvtgr6sfp	192.168.100.22	2026-06-11 14:05:55.968754+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313535353b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:ibd30d9id63lti50v5rp8bkoomocs01l	192.168.100.22	2026-06-11 14:08:19.598421+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313639393b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f64617368626f617264223b69647c733a313a2233223b757365726e616d657c733a353a2273616c6573223b6e616d617c733a31323a2253616c657320506572736f6e223b656d61696c7c733a31363a2273616c65734062616b6572792e636f6d223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:n66ml0re1cdqsa3j579g4i71mj7ttptl	192.168.100.106	2026-06-11 14:09:29.205663+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136313736393b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f64617368626f617264223b69647c733a313a2234223b757365726e616d657c733a31313a2273616c65732e616e616e67223b6e616d617c733a363a22616e616e6720223b656d61696c7c733a31353a22616e616e6740676d61696c2e636f6d223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:etuk3p40d6j64igim1ipd047n5dmo9eq	192.168.100.106	2026-06-11 14:14:55.478641+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136323039353b5f63695f70726576696f75735f75726c7c733a35393a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c61706f72616e2f70656e6a75616c616e2d73617961223b69647c733a313a2234223b757365726e616d657c733a31313a2273616c65732e616e616e67223b6e616d617c733a363a22616e616e6720223b656d61696c7c733a31353a22616e616e6740676d61696c2e636f6d223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:j5cqiokkp2v7kgle5e5iqkogdk3q533s	192.168.100.106	2026-06-11 14:23:02.659723+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136323538323b5f63695f70726576696f75735f75726c7c733a36303a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c61706f72616e2f73746f6b2d73616c65732d73617961223b69647c733a313a2234223b757365726e616d657c733a31313a2273616c65732e616e616e67223b6e616d617c733a363a22616e616e6720223b656d61696c7c733a31353a22616e616e6740676d61696c2e636f6d223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:ppfht9e86dpmnhcn2o38gfe61uktvr50	192.168.100.22	2026-06-11 14:25:09.083911+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136323730393b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f70656d62656c69616e223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:r5qduj7cfisonckunofq8no0lah9kss6	127.0.0.1	2026-06-11 14:29:41.587318+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136323938313b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:3lsnuv57ho7macig2h2db13fv1sohvi6	127.0.0.1	2026-06-11 14:29:41.904498+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136323938313b
ci_session:5qoqe1pcbrlqtatd7u8qlj2mbi43da2s	127.0.0.1	2026-06-11 14:30:23.234412+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136333032333b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:juf3suo4g7ia4jtidvqp87mhrpnv5s48	192.168.100.22	2026-06-11 14:50:46.821334+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136343234363b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c6f67696e223b
ci_session:uofsaf14e3ogl22t8re5vvhs52qe6e1r	192.168.100.22	2026-06-11 14:56:07.155319+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136343536373b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f70656e69746970616e223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:kqnbqmh2aajblgbt6kl3oru8mts21fr6	192.168.100.106	2026-06-11 15:01:48.464259+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136343930383b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6b756e6a756e67616e223b69647c733a313a2233223b757365726e616d657c733a353a2273616c6573223b6e616d617c733a31323a2253616c657320506572736f6e223b656d61696c7c733a31363a2273616c65734062616b6572792e636f6d223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:npvcka01jvpvbqu9phmc470pbjjf62ag	192.168.100.22	2026-06-11 15:02:32.127341+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136343935323b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f70656e69746970616e223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:1cashmd8hm6bchs2ggb8vfqdgmtbt6uo	192.168.100.22	2026-06-11 15:07:48.191281+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136353236383b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f64617368626f617264223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:d84n9rt7mk4qd3tgcqr95ju63l1ldlvh	192.168.100.22	2026-06-11 15:18:26.908153+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136353930363b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f64617368626f617264223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:fhc0fpr0ilh8ukmnckhj7sn5v5hfqqhi	192.168.100.22	2026-06-11 15:24:23.528073+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136363236333b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f70656d62656c69616e2f637265617465223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:lus4o5na4veq5heta45nu9tatci6bu4p	192.168.100.22	2026-06-11 15:29:28.573666+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136363536383b5f63695f70726576696f75735f75726c7c733a35353a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c61706f72616e2f73746f6b2d73616c6573223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
ci_session:0vj8i2mn6h816557a14scohtek8iflrg	192.168.100.106	2026-06-11 15:30:21.205925+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136363632313b5f63695f70726576696f75735f75726c7c733a35393a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c61706f72616e2f70656e6a75616c616e2d73617961223b69647c733a313a2233223b757365726e616d657c733a353a2273616c6573223b6e616d617c733a31323a2253616c657320506572736f6e223b656d61696c7c733a31363a2273616c65734062616b6572792e636f6d223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:d8k0qebde6joc5f4030kii4n88t3b0qc	192.168.100.22	2026-06-11 15:34:58.969352+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136363839383b5f63695f70726576696f75735f75726c7c733a34313a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f746f6b6f223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31343a2261646d696e40726f74692e636f6d223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b737563636573737c733a32323a22546f6b6f20626572686173696c206469757064617465223b5f5f63695f766172737c613a313a7b733a373a2273756363657373223b733a333a226f6c64223b7d
ci_session:qucjvigu12kd51u01c2cjbnnvg6ufaog	192.168.100.106	2026-06-11 15:36:30.633765+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136363939303b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f70656e69746970616e2f637265617465223b69647c733a313a2234223b757365726e616d657c733a343a2264657769223b6e616d617c733a31323a22446577692053617274696b61223b656d61696c7c733a31373a22646577694062616b6572792e636f2e6964223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:pg3mmg7olrrg35ogkqqqpmvefdmno88v	192.168.100.22	2026-06-11 15:40:22.893041+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136373232323b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6b756e6a756e67616e2f637265617465223b69647c733a313a2232223b757365726e616d657c733a353a2273616c6573223b6e616d617c733a31323a2253616c657320506572736f6e223b656d61696c7c733a31383a2273616c65734062616b6572792e636f2e6964223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:j6dbjcce6qirnvna2kiat7smun0dnf03	192.168.100.106	2026-06-11 15:42:23.100759+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136373334333b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6b756e6a756e67616e2f637265617465223b69647c733a313a2234223b757365726e616d657c733a343a2264657769223b6e616d617c733a31323a22446577692053617274696b61223b656d61696c7c733a31373a22646577694062616b6572792e636f2e6964223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:0en6bduslck47s7p34p5cli6u6radue7	192.168.100.22	2026-06-11 15:48:07.487388+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136373638373b5f63695f70726576696f75735f75726c7c733a34363a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f70656e69746970616e223b69647c733a313a2234223b757365726e616d657c733a343a2264657769223b6e616d617c733a31323a22446577692053617274696b61223b656d61696c7c733a31373a22646577694062616b6572792e636f2e6964223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:g41oqeiasm9ho5l3jhqshie4mh75ek7f	192.168.100.106	2026-06-11 15:48:24.203175+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136373730343b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6b756e6a756e67616e2f637265617465223b69647c733a313a2234223b757365726e616d657c733a343a2264657769223b6e616d617c733a31323a22446577692053617274696b61223b656d61696c7c733a31373a22646577694062616b6572792e636f2e6964223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:u3hlg4ddrpfdivtc5sk86je426bofeej	192.168.100.106	2026-06-11 15:51:18.177762+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136373730343b5f63695f70726576696f75735f75726c7c733a36303a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f6c61706f72616e2f73746f6b2d73616c65732d73617961223b69647c733a313a2234223b757365726e616d657c733a343a2264657769223b6e616d617c733a31323a22446577692053617274696b61223b656d61696c7c733a31373a22646577694062616b6572792e636f2e6964223b726f6c657c733a353a2273616c6573223b69734c6f67676564496e7c623a313b
ci_session:0ip7uh2rk9epovak5qum3rchp7tmati1	192.168.100.22	2026-06-11 15:51:41.442164+07	\\x5f5f63695f6c6173745f726567656e65726174657c693a313738313136373731303b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f3139322e3136382e3130302e32323a383038302f696e6465782e7068702f7265747572223b69647c733a313a2231223b757365726e616d657c733a353a2261646d696e223b6e616d617c733a31333a2241646d696e6973747261746f72223b656d61696c7c733a31383a2261646d696e4062616b6572792e636f2e6964223b726f6c657c733a353a2261646d696e223b69734c6f67676564496e7c623a313b
\.


--
-- TOC entry 3322 (class 0 OID 1494184)
-- Dependencies: 212
-- Data for Name: harga_jual; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.harga_jual (id, produk_id, nama_harga, harga, fee_sales, is_aktif, created_at, updated_at) FROM stdin;
1	1	Harga 1	5000.00	500.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
2	1	Harga 2	5500.00	550.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
3	2	Harga 1	7000.00	700.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
4	3	Harga 1	5000.00	500.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
5	4	Harga 1	5000.00	500.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
6	5	Harga 1	5000.00	500.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
7	6	Harga 1	6500.00	650.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
8	7	Harga 1	6500.00	650.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
9	8	Harga 1	10000.00	1000.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
10	9	Harga 1	9000.00	900.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
11	10	Harga 1	45000.00	4500.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
12	11	Harga 1	45000.00	4500.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
13	12	Harga 1	40000.00	4000.00	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
\.


--
-- TOC entry 3318 (class 0 OID 1494148)
-- Dependencies: 208
-- Data for Name: kategori_produk; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kategori_produk (id, nama, deskripsi, created_at) FROM stdin;
1	Roti Tawar	Roti dengan tekstur lembut, cocok untuk sandwich	2026-06-11 15:16:38.137208
2	Roti Manis	Roti dengan rasa manis, berbagai isian	2026-06-11 15:16:38.137208
3	Roti Sobek	Roti sobek lembut dengan topping	2026-06-11 15:16:38.137208
4	Pastry	Kue pastry seperti croissant, puff pastry	2026-06-11 15:16:38.137208
5	Kue Kering	Kue kering seperti nastar, kastengel	2026-06-11 15:16:38.137208
\.


--
-- TOC entry 3340 (class 0 OID 1494388)
-- Dependencies: 230
-- Data for Name: kunjungan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kunjungan (id, nomor_kunjungan, toko_id, sales_id, tanggal, status, catatan, created_at, updated_at) FROM stdin;
1	KJ-2606-0001	5	4	2026-06-11	selesai		2026-06-11 08:48:24	2026-06-11 08:48:24
2	KJ-2606-0002	5	4	2026-06-11	selesai		2026-06-11 08:50:44	2026-06-11 08:50:44
\.


--
-- TOC entry 3360 (class 0 OID 1494736)
-- Dependencies: 258
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, version, class, "group", namespace, "time", batch) FROM stdin;
\.


--
-- TOC entry 3332 (class 0 OID 1494291)
-- Dependencies: 222
-- Data for Name: mutasi_gudang; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.mutasi_gudang (id, produk_id, jenis, jumlah, referensi_id, referensi_tabel, keterangan, dibuat_oleh, created_at) FROM stdin;
1	1	masuk	100	1	pembelian	Penerimaan PO: PO-2606-0001	1	2026-06-11 08:25:25
2	2	masuk	100	1	pembelian	Penerimaan PO: PO-2606-0001	1	2026-06-11 08:25:25
3	3	masuk	100	1	pembelian	Penerimaan PO: PO-2606-0001	1	2026-06-11 08:25:25
4	4	masuk	100	1	pembelian	Penerimaan PO: PO-2606-0001	1	2026-06-11 08:25:25
5	1	kirim_ke_sales	100	1	pengiriman_ke_sales	Pengiriman ke sales: KS-2606-0001	1	2026-06-11 08:26:50
6	2	kirim_ke_sales	100	1	pengiriman_ke_sales	Pengiriman ke sales: KS-2606-0001	1	2026-06-11 08:26:50
7	3	kirim_ke_sales	100	1	pengiriman_ke_sales	Pengiriman ke sales: KS-2606-0001	1	2026-06-11 08:26:50
\.


--
-- TOC entry 3358 (class 0 OID 1494687)
-- Dependencies: 254
-- Data for Name: mutasi_sales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.mutasi_sales (id, sales_id, produk_id, jenis, jumlah, referensi_id, referensi_tabel, keterangan, created_at) FROM stdin;
1	4	1	masuk_dari_gudang	100	1	pengiriman_ke_sales	Penerimaan dari gudang: KS-2606-0001	2026-06-11 08:26:50
2	4	2	masuk_dari_gudang	100	1	pengiriman_ke_sales	Penerimaan dari gudang: KS-2606-0001	2026-06-11 08:26:50
3	4	3	masuk_dari_gudang	100	1	pengiriman_ke_sales	Penerimaan dari gudang: KS-2606-0001	2026-06-11 08:26:50
4	4	1	keluar_ke_toko	10	1	penitipan	Titip ke toko: TT-2606-0001	2026-06-11 08:45:23
5	4	2	keluar_ke_toko	10	1	penitipan	Titip ke toko: TT-2606-0001	2026-06-11 08:45:23
6	4	1	retur_dari_toko	1	1	retur	Retur baik: RT-2606-0001	2026-06-11 08:49:14
\.


--
-- TOC entry 3326 (class 0 OID 1494224)
-- Dependencies: 216
-- Data for Name: pembelian; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pembelian (id, nomor_po, supplier_id, tanggal_pesan, tanggal_terima, status, catatan, total_nilai, dibuat_oleh, created_at, updated_at) FROM stdin;
1	PO-2606-0001	3	2026-06-11	2026-06-11	diterima		860000.00	1	2026-06-11 08:24:23	2026-06-11 08:25:25
\.


--
-- TOC entry 3328 (class 0 OID 1494252)
-- Dependencies: 218
-- Data for Name: pembelian_detail; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pembelian_detail (id, pembelian_id, produk_id, jumlah_pesan, jumlah_terima, harga_beli, tgl_expired, created_at) FROM stdin;
1	1	1	100	100	2000.00	2026-06-16	2026-06-11 08:24:23
2	1	2	100	100	2100.00	2026-06-16	2026-06-11 08:24:23
3	1	3	100	100	2200.00	2026-06-16	2026-06-11 08:24:23
4	1	4	100	100	2300.00	2026-06-16	2026-06-11 08:24:23
\.


--
-- TOC entry 3354 (class 0 OID 1494627)
-- Dependencies: 250
-- Data for Name: pengiriman_ke_sales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pengiriman_ke_sales (id, nomor_kirim, sales_id, tanggal_kirim, catatan, dibuat_oleh, created_at, updated_at) FROM stdin;
1	KS-2606-0001	4	2026-06-11		1	2026-06-11 08:26:50	2026-06-11 08:26:50
\.


--
-- TOC entry 3356 (class 0 OID 1494653)
-- Dependencies: 252
-- Data for Name: pengiriman_ke_sales_detail; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pengiriman_ke_sales_detail (id, pengiriman_ke_sales_id, produk_id, jumlah, tgl_expired, harga_beli, created_at) FROM stdin;
1	1	1	100	2026-06-16	100.00	2026-06-11 08:26:50
2	1	2	100	2026-06-16	100.00	2026-06-11 08:26:50
3	1	3	100	2026-06-16	100.00	2026-06-11 08:26:50
\.


--
-- TOC entry 3334 (class 0 OID 1494313)
-- Dependencies: 224
-- Data for Name: penitipan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.penitipan (id, nomor_titip, toko_id, sales_id, tanggal_titip, status, catatan, created_at, updated_at) FROM stdin;
1	TT-2606-0001	5	4	2026-06-11	aktif		2026-06-11 08:45:23	2026-06-11 08:45:23
\.


--
-- TOC entry 3336 (class 0 OID 1494340)
-- Dependencies: 226
-- Data for Name: penitipan_detail; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.penitipan_detail (id, penitipan_id, produk_id, harga_jual_id, jumlah_titip, tgl_expired, harga_satuan, fee_satuan, created_at) FROM stdin;
1	1	1	1	10	2026-06-14	5000.00	500.00	2026-06-11 08:45:23
2	1	2	3	10	2026-06-14	7000.00	700.00	2026-06-11 08:45:23
\.


--
-- TOC entry 3342 (class 0 OID 1494415)
-- Dependencies: 232
-- Data for Name: penjualan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.penjualan (id, nomor_jual, kunjungan_id, toko_id, sales_id, tanggal, total_harga, total_fee, catatan, created_at) FROM stdin;
1	PJ-2606-0001	1	5	4	2026-06-11	35000.00	3500.00	\N	2026-06-11 08:48:24
2	PJ-2606-0002	2	5	4	2026-06-11	5000.00	500.00	\N	2026-06-11 08:50:44
\.


--
-- TOC entry 3344 (class 0 OID 1494447)
-- Dependencies: 234
-- Data for Name: penjualan_detail; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.penjualan_detail (id, penjualan_id, produk_id, harga_jual_id, jumlah_terjual, harga_satuan, fee_satuan, hpp_satuan, created_at) FROM stdin;
1	1	1	1	7	5000.00	500.00	3500.00	2026-06-11 08:48:24
2	2	1	1	1	5000.00	500.00	3500.00	2026-06-11 08:50:44
\.


--
-- TOC entry 3320 (class 0 OID 1494160)
-- Dependencies: 210
-- Data for Name: produk; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.produk (id, kode_produk, nama, kategori_id, satuan, hpp, shelf_life_hari, deskripsi, foto, is_aktif, created_at, updated_at) FROM stdin;
1	PRD001	Roti Tawar 50gr	1	pcs	3500.00	3	Roti tawar putih 50 gram per pcs	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
2	PRD002	Roti Tawar 100gr	1	pcs	5000.00	3	Roti tawar putih 100 gram per pcs	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
3	PRD003	Roti Manis Cokelat	2	pcs	3000.00	3	Roti manis isi cokelat	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
4	PRD004	Roti Manis Keju	2	pcs	3000.00	3	Roti manis isi keju	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
5	PRD005	Roti Manis Kacang	2	pcs	3000.00	3	Roti manis isi kacang hijau	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
6	PRD006	Roti Sobek Cokelat	3	pcs	4000.00	3	Roti sobek topping cokelat	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
7	PRD007	Roti Sobek Keju	3	pcs	4000.00	3	Roti sobek topping keju	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
8	PRD008	Croissant	4	pcs	6000.00	2	Croissant mentega	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
9	PRD009	Puff Pastry	4	pcs	5500.00	2	Pastry isi daging/ayam	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
10	PRD010	Nastar	5	toples	25000.00	14	Kue nastar nanas per toples	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
11	PRD011	Kastengel	5	toples	25000.00	14	Kue kastengel keju per toples	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
12	PRD012	Putri Salju	5	toples	23000.00	14	Kue putri salju per toples	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
\.


--
-- TOC entry 3346 (class 0 OID 1494477)
-- Dependencies: 236
-- Data for Name: retur; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.retur (id, nomor_retur, kunjungan_id, toko_id, sales_id, tanggal, status, alasan, catatan, created_at, updated_at) FROM stdin;
1	RT-2606-0001	1	5	4	2026-06-11	disetujui	Retur dari toko	\N	2026-06-11 08:48:24	2026-06-11 08:49:14
2	RT-2606-0002	2	5	4	2026-06-11	disetujui	Retur dari toko	\N	2026-06-11 08:50:44	2026-06-11 08:50:58
\.


--
-- TOC entry 3348 (class 0 OID 1494509)
-- Dependencies: 238
-- Data for Name: retur_detail; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.retur_detail (id, retur_id, produk_id, jumlah_retur, kondisi, tgl_expired, keterangan, created_at) FROM stdin;
1	1	1	1	baik	\N		2026-06-11 08:48:24
2	2	1	1	expired	\N		2026-06-11 08:50:44
\.


--
-- TOC entry 3350 (class 0 OID 1494533)
-- Dependencies: 240
-- Data for Name: stok_expired_toko; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stok_expired_toko (id, toko_id, produk_id, penitipan_detail_id, jumlah, tgl_expired, is_diretur, created_at) FROM stdin;
1	5	1	1	10	2026-06-14	f	2026-06-11 08:45:23
2	5	2	2	10	2026-06-14	f	2026-06-11 08:45:23
\.


--
-- TOC entry 3330 (class 0 OID 1494274)
-- Dependencies: 220
-- Data for Name: stok_gudang; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stok_gudang (id, produk_id, stok_tersedia, updated_at) FROM stdin;
5	5	500	2026-06-11 15:16:38.137208
6	6	300	2026-06-11 15:16:38.137208
7	7	300	2026-06-11 15:16:38.137208
8	8	200	2026-06-11 15:16:38.137208
9	9	150	2026-06-11 15:16:38.137208
10	10	100	2026-06-11 15:16:38.137208
11	11	100	2026-06-11 15:16:38.137208
12	12	100	2026-06-11 15:16:38.137208
4	4	600	2026-06-11 08:25:25
1	1	2000	2026-06-11 08:26:50
2	2	500	2026-06-11 08:26:50
3	3	1000	2026-06-11 08:26:50
\.


--
-- TOC entry 3352 (class 0 OID 1494605)
-- Dependencies: 248
-- Data for Name: stok_sales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stok_sales (id, sales_id, produk_id, stok_tersedia, updated_at) FROM stdin;
1	2	1	0	2026-06-11 15:16:38.137208
2	2	2	0	2026-06-11 15:16:38.137208
3	2	3	0	2026-06-11 15:16:38.137208
4	2	4	0	2026-06-11 15:16:38.137208
5	2	5	0	2026-06-11 15:16:38.137208
6	2	6	0	2026-06-11 15:16:38.137208
7	2	7	0	2026-06-11 15:16:38.137208
8	2	8	0	2026-06-11 15:16:38.137208
9	2	9	0	2026-06-11 15:16:38.137208
10	2	10	0	2026-06-11 15:16:38.137208
11	2	11	0	2026-06-11 15:16:38.137208
12	2	12	0	2026-06-11 15:16:38.137208
13	3	1	0	2026-06-11 15:16:38.137208
14	3	2	0	2026-06-11 15:16:38.137208
15	3	3	0	2026-06-11 15:16:38.137208
16	3	4	0	2026-06-11 15:16:38.137208
17	3	5	0	2026-06-11 15:16:38.137208
18	3	6	0	2026-06-11 15:16:38.137208
19	3	7	0	2026-06-11 15:16:38.137208
20	3	8	0	2026-06-11 15:16:38.137208
21	3	9	0	2026-06-11 15:16:38.137208
22	3	10	0	2026-06-11 15:16:38.137208
23	3	11	0	2026-06-11 15:16:38.137208
24	3	12	0	2026-06-11 15:16:38.137208
28	4	4	0	2026-06-11 15:16:38.137208
29	4	5	0	2026-06-11 15:16:38.137208
30	4	6	0	2026-06-11 15:16:38.137208
31	4	7	0	2026-06-11 15:16:38.137208
32	4	8	0	2026-06-11 15:16:38.137208
33	4	9	0	2026-06-11 15:16:38.137208
34	4	10	0	2026-06-11 15:16:38.137208
35	4	11	0	2026-06-11 15:16:38.137208
36	4	12	0	2026-06-11 15:16:38.137208
27	4	3	100	2026-06-11 08:26:50
26	4	2	90	2026-06-11 08:45:23
25	4	1	91	2026-06-11 08:49:14
\.


--
-- TOC entry 3338 (class 0 OID 1494366)
-- Dependencies: 228
-- Data for Name: stok_toko; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stok_toko (id, toko_id, produk_id, stok_tersedia, updated_at) FROM stdin;
2	5	2	10	2026-06-11 08:45:23
1	5	1	0	2026-06-11 08:50:58
\.


--
-- TOC entry 3316 (class 0 OID 1494132)
-- Dependencies: 206
-- Data for Name: suppliers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.suppliers (id, kode_supplier, nama, alamat, telepon, email, kontak_person, is_aktif, created_at, updated_at) FROM stdin;
1	SUP001	PT Berkah Jaya	Jl. Merdeka No. 10, Jakarta	021-5551234	berkah@email.com	Budi Santoso	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
2	SUP002	CV Sumber Makmur	Jl. Sudirman No. 25, Bandung	022-6667890	sumber@email.com	Ani Wijaya	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
3	SUP003	PD Tepung Sejahtera	Jl. Ahmad Yani No. 8, Surabaya	031-7776543	tepung@email.com	Citra Dewi	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
\.


--
-- TOC entry 3324 (class 0 OID 1494203)
-- Dependencies: 214
-- Data for Name: toko; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.toko (id, kode_toko, nama, pemilik, alamat, kelurahan, kecamatan, kota, telepon, sales_id, is_aktif, created_at, updated_at) FROM stdin;
1	TKO0001	Toko Berkah	Rudi Hartono	Jl. Merdeka No. 5, Bandung	Sukawarna	Sukajadi	Bandung	081234567891	2	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
2	TKO0002	Toko Makmur	Siti Rahmawati	Jl. Sudirman No. 12, Bandung	Cihapit	Bandung Kidul	Bandung	081234567892	2	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
3	TKO0003	Toko Sejahtera	Ahmad Fauzi	Jl. Diponegoro No. 8, Bandung	Kebon Jeruk	Andir	Bandung	081234567893	3	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
4	TKO0004	Toko Jaya Abadi	Dewi Lestari	Jl. Asia Afrika No. 15, Bandung	Braga	Sumur Bandung	Bandung	081234567894	3	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
5	TKO0005	Toko Harapan Indah	Bambang Sutejo	Jl. Raya Kopo No. 20, Bandung	Mekarwangi	Bojongloa Kidul	Bandung	081234567895	4	t	2026-06-11 15:16:38.137208	2026-06-11 08:31:16
\.


--
-- TOC entry 3314 (class 0 OID 1494113)
-- Dependencies: 204
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, nama, username, password, role, email, telepon, foto, is_aktif, created_at, updated_at) FROM stdin;
1	Administrator	admin	$2y$10$arBbwCYEAdeu/RBXn4E2VeOAd1370O.MAHbslqqKFGYrJAhn/Shf2	admin	admin@bakery.co.id	081234567890	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
2	Sales Person	sales	$2y$10$iODOyrL3OmPKgEqG52zN0uAcLIZx0fxlDZ.xzRzWUsmrfCnLXKzFW	sales	sales@bakery.co.id	081234567891	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
3	Anang Hermawan	anang	$2y$10$1.TDa8vZQzQqDA17qziu5.fffnZJJviFAZ0HLqF/7C6PxBXl5l2MS	sales	anang@bakery.co.id	089619136616	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
4	Dewi Sartika	dewi	$2y$10$1.TDa8vZQzQqDA17qziu5.fffnZJJviFAZ0HLqF/7C6PxBXl5l2MS	sales	dewi@bakery.co.id	081234567892	\N	t	2026-06-11 15:16:38.137208	2026-06-11 15:16:38.137208
\.


--
-- TOC entry 3392 (class 0 OID 0)
-- Dependencies: 211
-- Name: harga_jual_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.harga_jual_id_seq', 13, true);


--
-- TOC entry 3393 (class 0 OID 0)
-- Dependencies: 207
-- Name: kategori_produk_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kategori_produk_id_seq', 5, true);


--
-- TOC entry 3394 (class 0 OID 0)
-- Dependencies: 229
-- Name: kunjungan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kunjungan_id_seq', 2, true);


--
-- TOC entry 3395 (class 0 OID 0)
-- Dependencies: 257
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 1, false);


--
-- TOC entry 3396 (class 0 OID 0)
-- Dependencies: 221
-- Name: mutasi_gudang_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mutasi_gudang_id_seq', 7, true);


--
-- TOC entry 3397 (class 0 OID 0)
-- Dependencies: 253
-- Name: mutasi_sales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mutasi_sales_id_seq', 6, true);


--
-- TOC entry 3398 (class 0 OID 0)
-- Dependencies: 217
-- Name: pembelian_detail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pembelian_detail_id_seq', 4, true);


--
-- TOC entry 3399 (class 0 OID 0)
-- Dependencies: 215
-- Name: pembelian_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pembelian_id_seq', 1, true);


--
-- TOC entry 3400 (class 0 OID 0)
-- Dependencies: 251
-- Name: pengiriman_ke_sales_detail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pengiriman_ke_sales_detail_id_seq', 3, true);


--
-- TOC entry 3401 (class 0 OID 0)
-- Dependencies: 249
-- Name: pengiriman_ke_sales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pengiriman_ke_sales_id_seq', 1, true);


--
-- TOC entry 3402 (class 0 OID 0)
-- Dependencies: 225
-- Name: penitipan_detail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.penitipan_detail_id_seq', 2, true);


--
-- TOC entry 3403 (class 0 OID 0)
-- Dependencies: 223
-- Name: penitipan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.penitipan_id_seq', 1, true);


--
-- TOC entry 3404 (class 0 OID 0)
-- Dependencies: 233
-- Name: penjualan_detail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.penjualan_detail_id_seq', 2, true);


--
-- TOC entry 3405 (class 0 OID 0)
-- Dependencies: 231
-- Name: penjualan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.penjualan_id_seq', 2, true);


--
-- TOC entry 3406 (class 0 OID 0)
-- Dependencies: 209
-- Name: produk_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.produk_id_seq', 12, true);


--
-- TOC entry 3407 (class 0 OID 0)
-- Dependencies: 237
-- Name: retur_detail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.retur_detail_id_seq', 2, true);


--
-- TOC entry 3408 (class 0 OID 0)
-- Dependencies: 235
-- Name: retur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.retur_id_seq', 2, true);


--
-- TOC entry 3409 (class 0 OID 0)
-- Dependencies: 239
-- Name: stok_expired_toko_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stok_expired_toko_id_seq', 2, true);


--
-- TOC entry 3410 (class 0 OID 0)
-- Dependencies: 219
-- Name: stok_gudang_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stok_gudang_id_seq', 12, true);


--
-- TOC entry 3411 (class 0 OID 0)
-- Dependencies: 247
-- Name: stok_sales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stok_sales_id_seq', 36, true);


--
-- TOC entry 3412 (class 0 OID 0)
-- Dependencies: 227
-- Name: stok_toko_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stok_toko_id_seq', 2, true);


--
-- TOC entry 3413 (class 0 OID 0)
-- Dependencies: 205
-- Name: suppliers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.suppliers_id_seq', 3, true);


--
-- TOC entry 3414 (class 0 OID 0)
-- Dependencies: 213
-- Name: toko_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.toko_id_seq', 5, true);


--
-- TOC entry 3415 (class 0 OID 0)
-- Dependencies: 203
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- TOC entry 3136 (class 2606 OID 1499874)
-- Name: ci_sessions ci_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ci_sessions
    ADD CONSTRAINT ci_sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 3031 (class 2606 OID 1494193)
-- Name: harga_jual harga_jual_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.harga_jual
    ADD CONSTRAINT harga_jual_pkey PRIMARY KEY (id);


--
-- TOC entry 3033 (class 2606 OID 1494195)
-- Name: harga_jual harga_jual_produk_id_nama_harga_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.harga_jual
    ADD CONSTRAINT harga_jual_produk_id_nama_harga_key UNIQUE (produk_id, nama_harga);


--
-- TOC entry 3024 (class 2606 OID 1494157)
-- Name: kategori_produk kategori_produk_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_produk
    ADD CONSTRAINT kategori_produk_pkey PRIMARY KEY (id);


--
-- TOC entry 3083 (class 2606 OID 1494402)
-- Name: kunjungan kunjungan_nomor_kunjungan_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kunjungan
    ADD CONSTRAINT kunjungan_nomor_kunjungan_key UNIQUE (nomor_kunjungan);


--
-- TOC entry 3085 (class 2606 OID 1494400)
-- Name: kunjungan kunjungan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kunjungan
    ADD CONSTRAINT kunjungan_pkey PRIMARY KEY (id);


--
-- TOC entry 3058 (class 2606 OID 1494300)
-- Name: mutasi_gudang mutasi_gudang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_gudang
    ADD CONSTRAINT mutasi_gudang_pkey PRIMARY KEY (id);


--
-- TOC entry 3132 (class 2606 OID 1494696)
-- Name: mutasi_sales mutasi_sales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_sales
    ADD CONSTRAINT mutasi_sales_pkey PRIMARY KEY (id);


--
-- TOC entry 3050 (class 2606 OID 1494261)
-- Name: pembelian_detail pembelian_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian_detail
    ADD CONSTRAINT pembelian_detail_pkey PRIMARY KEY (id);


--
-- TOC entry 3043 (class 2606 OID 1494239)
-- Name: pembelian pembelian_nomor_po_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian
    ADD CONSTRAINT pembelian_nomor_po_key UNIQUE (nomor_po);


--
-- TOC entry 3045 (class 2606 OID 1494237)
-- Name: pembelian pembelian_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian
    ADD CONSTRAINT pembelian_pkey PRIMARY KEY (id);


--
-- TOC entry 3128 (class 2606 OID 1494661)
-- Name: pengiriman_ke_sales_detail pengiriman_ke_sales_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales_detail
    ADD CONSTRAINT pengiriman_ke_sales_detail_pkey PRIMARY KEY (id);


--
-- TOC entry 3122 (class 2606 OID 1494640)
-- Name: pengiriman_ke_sales pengiriman_ke_sales_nomor_kirim_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales
    ADD CONSTRAINT pengiriman_ke_sales_nomor_kirim_key UNIQUE (nomor_kirim);


--
-- TOC entry 3124 (class 2606 OID 1494638)
-- Name: pengiriman_ke_sales pengiriman_ke_sales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales
    ADD CONSTRAINT pengiriman_ke_sales_pkey PRIMARY KEY (id);


--
-- TOC entry 3072 (class 2606 OID 1494348)
-- Name: penitipan_detail penitipan_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan_detail
    ADD CONSTRAINT penitipan_detail_pkey PRIMARY KEY (id);


--
-- TOC entry 3065 (class 2606 OID 1494327)
-- Name: penitipan penitipan_nomor_titip_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan
    ADD CONSTRAINT penitipan_nomor_titip_key UNIQUE (nomor_titip);


--
-- TOC entry 3067 (class 2606 OID 1494325)
-- Name: penitipan penitipan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan
    ADD CONSTRAINT penitipan_pkey PRIMARY KEY (id);


--
-- TOC entry 3098 (class 2606 OID 1494459)
-- Name: penjualan_detail penjualan_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan_detail
    ADD CONSTRAINT penjualan_detail_pkey PRIMARY KEY (id);


--
-- TOC entry 3091 (class 2606 OID 1494429)
-- Name: penjualan penjualan_nomor_jual_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan
    ADD CONSTRAINT penjualan_nomor_jual_key UNIQUE (nomor_jual);


--
-- TOC entry 3093 (class 2606 OID 1494427)
-- Name: penjualan penjualan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan
    ADD CONSTRAINT penjualan_pkey PRIMARY KEY (id);


--
-- TOC entry 3134 (class 2606 OID 1494744)
-- Name: migrations pk_migrations; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT pk_migrations PRIMARY KEY (id);


--
-- TOC entry 3027 (class 2606 OID 1494176)
-- Name: produk produk_kode_produk_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT produk_kode_produk_key UNIQUE (kode_produk);


--
-- TOC entry 3029 (class 2606 OID 1494174)
-- Name: produk produk_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT produk_pkey PRIMARY KEY (id);


--
-- TOC entry 3108 (class 2606 OID 1494520)
-- Name: retur_detail retur_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur_detail
    ADD CONSTRAINT retur_detail_pkey PRIMARY KEY (id);


--
-- TOC entry 3102 (class 2606 OID 1494491)
-- Name: retur retur_nomor_retur_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur
    ADD CONSTRAINT retur_nomor_retur_key UNIQUE (nomor_retur);


--
-- TOC entry 3104 (class 2606 OID 1494489)
-- Name: retur retur_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur
    ADD CONSTRAINT retur_pkey PRIMARY KEY (id);


--
-- TOC entry 3111 (class 2606 OID 1494541)
-- Name: stok_expired_toko stok_expired_toko_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_expired_toko
    ADD CONSTRAINT stok_expired_toko_pkey PRIMARY KEY (id);


--
-- TOC entry 3052 (class 2606 OID 1494281)
-- Name: stok_gudang stok_gudang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_gudang
    ADD CONSTRAINT stok_gudang_pkey PRIMARY KEY (id);


--
-- TOC entry 3054 (class 2606 OID 1494283)
-- Name: stok_gudang stok_gudang_produk_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_gudang
    ADD CONSTRAINT stok_gudang_produk_id_key UNIQUE (produk_id);


--
-- TOC entry 3115 (class 2606 OID 1494612)
-- Name: stok_sales stok_sales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_sales
    ADD CONSTRAINT stok_sales_pkey PRIMARY KEY (id);


--
-- TOC entry 3117 (class 2606 OID 1494614)
-- Name: stok_sales stok_sales_sales_id_produk_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_sales
    ADD CONSTRAINT stok_sales_sales_id_produk_id_key UNIQUE (sales_id, produk_id);


--
-- TOC entry 3075 (class 2606 OID 1494373)
-- Name: stok_toko stok_toko_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_toko
    ADD CONSTRAINT stok_toko_pkey PRIMARY KEY (id);


--
-- TOC entry 3077 (class 2606 OID 1494375)
-- Name: stok_toko stok_toko_toko_id_produk_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_toko
    ADD CONSTRAINT stok_toko_toko_id_produk_id_key UNIQUE (toko_id, produk_id);


--
-- TOC entry 3020 (class 2606 OID 1494145)
-- Name: suppliers suppliers_kode_supplier_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_kode_supplier_key UNIQUE (kode_supplier);


--
-- TOC entry 3022 (class 2606 OID 1494143)
-- Name: suppliers suppliers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id);


--
-- TOC entry 3038 (class 2606 OID 1494216)
-- Name: toko toko_kode_toko_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.toko
    ADD CONSTRAINT toko_kode_toko_key UNIQUE (kode_toko);


--
-- TOC entry 3040 (class 2606 OID 1494214)
-- Name: toko toko_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.toko
    ADD CONSTRAINT toko_pkey PRIMARY KEY (id);


--
-- TOC entry 3013 (class 2606 OID 1494129)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3015 (class 2606 OID 1494125)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3017 (class 2606 OID 1494127)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 3137 (class 1259 OID 1499875)
-- Name: ci_sessions_timestamp_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX ci_sessions_timestamp_idx ON public.ci_sessions USING btree ("timestamp");


--
-- TOC entry 3094 (class 1259 OID 1494572)
-- Name: idx_detail_penjualan; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_detail_penjualan ON public.penjualan_detail USING btree (penjualan_id);


--
-- TOC entry 3109 (class 1259 OID 1494570)
-- Name: idx_expired_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_expired_tgl ON public.stok_expired_toko USING btree (tgl_expired);


--
-- TOC entry 3034 (class 1259 OID 1494571)
-- Name: idx_harga_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_harga_produk ON public.harga_jual USING btree (produk_id);


--
-- TOC entry 3078 (class 1259 OID 1494564)
-- Name: idx_kunjungan_sales; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_kunjungan_sales ON public.kunjungan USING btree (sales_id);


--
-- TOC entry 3079 (class 1259 OID 1499886)
-- Name: idx_kunjungan_sales_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_kunjungan_sales_tgl ON public.kunjungan USING btree (sales_id, tanggal);


--
-- TOC entry 3080 (class 1259 OID 1499894)
-- Name: idx_kunjungan_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_kunjungan_status ON public.kunjungan USING btree (status);


--
-- TOC entry 3081 (class 1259 OID 1494565)
-- Name: idx_kunjungan_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_kunjungan_tgl ON public.kunjungan USING btree (tanggal);


--
-- TOC entry 3055 (class 1259 OID 1494567)
-- Name: idx_mutasi_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mutasi_produk ON public.mutasi_gudang USING btree (produk_id);


--
-- TOC entry 3129 (class 1259 OID 1494712)
-- Name: idx_mutasi_sales_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mutasi_sales_produk ON public.mutasi_sales USING btree (produk_id);


--
-- TOC entry 3130 (class 1259 OID 1494711)
-- Name: idx_mutasi_sales_sales; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mutasi_sales_sales ON public.mutasi_sales USING btree (sales_id);


--
-- TOC entry 3056 (class 1259 OID 1494568)
-- Name: idx_mutasi_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mutasi_tgl ON public.mutasi_gudang USING btree (created_at);


--
-- TOC entry 3046 (class 1259 OID 1494573)
-- Name: idx_pembelian_detail; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pembelian_detail ON public.pembelian_detail USING btree (pembelian_id);


--
-- TOC entry 3047 (class 1259 OID 1499882)
-- Name: idx_pembelian_detail_beli; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pembelian_detail_beli ON public.pembelian_detail USING btree (pembelian_id);


--
-- TOC entry 3048 (class 1259 OID 1499883)
-- Name: idx_pembelian_detail_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pembelian_detail_produk ON public.pembelian_detail USING btree (produk_id);


--
-- TOC entry 3041 (class 1259 OID 1499895)
-- Name: idx_pembelian_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pembelian_status ON public.pembelian USING btree (status);


--
-- TOC entry 3125 (class 1259 OID 1499884)
-- Name: idx_pengiriman_detail_kirim; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengiriman_detail_kirim ON public.pengiriman_ke_sales_detail USING btree (pengiriman_ke_sales_id);


--
-- TOC entry 3126 (class 1259 OID 1499885)
-- Name: idx_pengiriman_detail_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengiriman_detail_produk ON public.pengiriman_ke_sales_detail USING btree (produk_id);


--
-- TOC entry 3118 (class 1259 OID 1494709)
-- Name: idx_pengiriman_sales; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengiriman_sales ON public.pengiriman_ke_sales USING btree (sales_id);


--
-- TOC entry 3119 (class 1259 OID 1499889)
-- Name: idx_pengiriman_sales_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengiriman_sales_tgl ON public.pengiriman_ke_sales USING btree (sales_id, tanggal_kirim);


--
-- TOC entry 3120 (class 1259 OID 1494710)
-- Name: idx_pengiriman_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengiriman_tgl ON public.pengiriman_ke_sales USING btree (tanggal_kirim);


--
-- TOC entry 3068 (class 1259 OID 1499898)
-- Name: idx_penitipan_detail_expired; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_detail_expired ON public.penitipan_detail USING btree (tgl_expired);


--
-- TOC entry 3069 (class 1259 OID 1499881)
-- Name: idx_penitipan_detail_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_detail_produk ON public.penitipan_detail USING btree (produk_id);


--
-- TOC entry 3070 (class 1259 OID 1499880)
-- Name: idx_penitipan_detail_titip; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_detail_titip ON public.penitipan_detail USING btree (penitipan_id);


--
-- TOC entry 3059 (class 1259 OID 1494559)
-- Name: idx_penitipan_sales; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_sales ON public.penitipan USING btree (sales_id);


--
-- TOC entry 3060 (class 1259 OID 1499888)
-- Name: idx_penitipan_sales_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_sales_tgl ON public.penitipan USING btree (sales_id, tanggal_titip);


--
-- TOC entry 3061 (class 1259 OID 1494560)
-- Name: idx_penitipan_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_tgl ON public.penitipan USING btree (tanggal_titip);


--
-- TOC entry 3062 (class 1259 OID 1494558)
-- Name: idx_penitipan_toko; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_toko ON public.penitipan USING btree (toko_id);


--
-- TOC entry 3063 (class 1259 OID 1499897)
-- Name: idx_penitipan_toko_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penitipan_toko_id ON public.penitipan USING btree (toko_id);


--
-- TOC entry 3095 (class 1259 OID 1499876)
-- Name: idx_penjualan_detail_jual; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penjualan_detail_jual ON public.penjualan_detail USING btree (penjualan_id);


--
-- TOC entry 3096 (class 1259 OID 1499877)
-- Name: idx_penjualan_detail_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penjualan_detail_produk ON public.penjualan_detail USING btree (produk_id);


--
-- TOC entry 3086 (class 1259 OID 1494562)
-- Name: idx_penjualan_sales; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penjualan_sales ON public.penjualan USING btree (sales_id);


--
-- TOC entry 3087 (class 1259 OID 1499887)
-- Name: idx_penjualan_sales_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penjualan_sales_tgl ON public.penjualan USING btree (sales_id, tanggal);


--
-- TOC entry 3088 (class 1259 OID 1494563)
-- Name: idx_penjualan_tgl; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penjualan_tgl ON public.penjualan USING btree (tanggal);


--
-- TOC entry 3089 (class 1259 OID 1494561)
-- Name: idx_penjualan_toko; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penjualan_toko ON public.penjualan USING btree (toko_id);


--
-- TOC entry 3025 (class 1259 OID 1499892)
-- Name: idx_produk_is_aktif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_produk_is_aktif ON public.produk USING btree (is_aktif);


--
-- TOC entry 3105 (class 1259 OID 1499879)
-- Name: idx_retur_detail_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_retur_detail_produk ON public.retur_detail USING btree (produk_id);


--
-- TOC entry 3106 (class 1259 OID 1499878)
-- Name: idx_retur_detail_retur; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_retur_detail_retur ON public.retur_detail USING btree (retur_id);


--
-- TOC entry 3099 (class 1259 OID 1499896)
-- Name: idx_retur_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_retur_status ON public.retur USING btree (status);


--
-- TOC entry 3100 (class 1259 OID 1494566)
-- Name: idx_retur_toko; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_retur_toko ON public.retur USING btree (toko_id);


--
-- TOC entry 3112 (class 1259 OID 1494708)
-- Name: idx_stok_sales_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_stok_sales_produk ON public.stok_sales USING btree (sales_id, produk_id);


--
-- TOC entry 3113 (class 1259 OID 1494707)
-- Name: idx_stok_sales_sales; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_stok_sales_sales ON public.stok_sales USING btree (sales_id);


--
-- TOC entry 3073 (class 1259 OID 1494569)
-- Name: idx_stok_toko_produk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_stok_toko_produk ON public.stok_toko USING btree (toko_id, produk_id);


--
-- TOC entry 3018 (class 1259 OID 1499893)
-- Name: idx_suppliers_is_aktif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_suppliers_is_aktif ON public.suppliers USING btree (is_aktif);


--
-- TOC entry 3035 (class 1259 OID 1499890)
-- Name: idx_toko_is_aktif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_toko_is_aktif ON public.toko USING btree (is_aktif);


--
-- TOC entry 3036 (class 1259 OID 1494557)
-- Name: idx_toko_sales; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_toko_sales ON public.toko USING btree (sales_id);


--
-- TOC entry 3011 (class 1259 OID 1499891)
-- Name: idx_users_role_aktif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_role_aktif ON public.users USING btree (role, is_aktif);


--
-- TOC entry 3139 (class 2606 OID 1494196)
-- Name: harga_jual harga_jual_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.harga_jual
    ADD CONSTRAINT harga_jual_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id) ON DELETE CASCADE;


--
-- TOC entry 3156 (class 2606 OID 1494408)
-- Name: kunjungan kunjungan_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kunjungan
    ADD CONSTRAINT kunjungan_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


--
-- TOC entry 3155 (class 2606 OID 1494403)
-- Name: kunjungan kunjungan_toko_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kunjungan
    ADD CONSTRAINT kunjungan_toko_id_fkey FOREIGN KEY (toko_id) REFERENCES public.toko(id);


--
-- TOC entry 3147 (class 2606 OID 1494306)
-- Name: mutasi_gudang mutasi_gudang_dibuat_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_gudang
    ADD CONSTRAINT mutasi_gudang_dibuat_oleh_fkey FOREIGN KEY (dibuat_oleh) REFERENCES public.users(id);


--
-- TOC entry 3146 (class 2606 OID 1494301)
-- Name: mutasi_gudang mutasi_gudang_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_gudang
    ADD CONSTRAINT mutasi_gudang_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3178 (class 2606 OID 1494702)
-- Name: mutasi_sales mutasi_sales_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_sales
    ADD CONSTRAINT mutasi_sales_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3177 (class 2606 OID 1494697)
-- Name: mutasi_sales mutasi_sales_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mutasi_sales
    ADD CONSTRAINT mutasi_sales_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


--
-- TOC entry 3143 (class 2606 OID 1494262)
-- Name: pembelian_detail pembelian_detail_pembelian_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian_detail
    ADD CONSTRAINT pembelian_detail_pembelian_id_fkey FOREIGN KEY (pembelian_id) REFERENCES public.pembelian(id) ON DELETE CASCADE;


--
-- TOC entry 3144 (class 2606 OID 1494267)
-- Name: pembelian_detail pembelian_detail_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian_detail
    ADD CONSTRAINT pembelian_detail_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3142 (class 2606 OID 1494245)
-- Name: pembelian pembelian_dibuat_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian
    ADD CONSTRAINT pembelian_dibuat_oleh_fkey FOREIGN KEY (dibuat_oleh) REFERENCES public.users(id);


--
-- TOC entry 3141 (class 2606 OID 1494240)
-- Name: pembelian pembelian_supplier_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembelian
    ADD CONSTRAINT pembelian_supplier_id_fkey FOREIGN KEY (supplier_id) REFERENCES public.suppliers(id);


--
-- TOC entry 3175 (class 2606 OID 1494662)
-- Name: pengiriman_ke_sales_detail pengiriman_ke_sales_detail_pengiriman_ke_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales_detail
    ADD CONSTRAINT pengiriman_ke_sales_detail_pengiriman_ke_sales_id_fkey FOREIGN KEY (pengiriman_ke_sales_id) REFERENCES public.pengiriman_ke_sales(id) ON DELETE CASCADE;


--
-- TOC entry 3176 (class 2606 OID 1494667)
-- Name: pengiriman_ke_sales_detail pengiriman_ke_sales_detail_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales_detail
    ADD CONSTRAINT pengiriman_ke_sales_detail_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3174 (class 2606 OID 1494646)
-- Name: pengiriman_ke_sales pengiriman_ke_sales_dibuat_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales
    ADD CONSTRAINT pengiriman_ke_sales_dibuat_oleh_fkey FOREIGN KEY (dibuat_oleh) REFERENCES public.users(id);


--
-- TOC entry 3173 (class 2606 OID 1494641)
-- Name: pengiriman_ke_sales pengiriman_ke_sales_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengiriman_ke_sales
    ADD CONSTRAINT pengiriman_ke_sales_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


--
-- TOC entry 3152 (class 2606 OID 1494359)
-- Name: penitipan_detail penitipan_detail_harga_jual_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan_detail
    ADD CONSTRAINT penitipan_detail_harga_jual_id_fkey FOREIGN KEY (harga_jual_id) REFERENCES public.harga_jual(id);


--
-- TOC entry 3150 (class 2606 OID 1494349)
-- Name: penitipan_detail penitipan_detail_penitipan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan_detail
    ADD CONSTRAINT penitipan_detail_penitipan_id_fkey FOREIGN KEY (penitipan_id) REFERENCES public.penitipan(id) ON DELETE CASCADE;


--
-- TOC entry 3151 (class 2606 OID 1494354)
-- Name: penitipan_detail penitipan_detail_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan_detail
    ADD CONSTRAINT penitipan_detail_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3149 (class 2606 OID 1494333)
-- Name: penitipan penitipan_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan
    ADD CONSTRAINT penitipan_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


--
-- TOC entry 3148 (class 2606 OID 1494328)
-- Name: penitipan penitipan_toko_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penitipan
    ADD CONSTRAINT penitipan_toko_id_fkey FOREIGN KEY (toko_id) REFERENCES public.toko(id);


--
-- TOC entry 3162 (class 2606 OID 1494470)
-- Name: penjualan_detail penjualan_detail_harga_jual_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan_detail
    ADD CONSTRAINT penjualan_detail_harga_jual_id_fkey FOREIGN KEY (harga_jual_id) REFERENCES public.harga_jual(id);


--
-- TOC entry 3160 (class 2606 OID 1494460)
-- Name: penjualan_detail penjualan_detail_penjualan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan_detail
    ADD CONSTRAINT penjualan_detail_penjualan_id_fkey FOREIGN KEY (penjualan_id) REFERENCES public.penjualan(id) ON DELETE CASCADE;


--
-- TOC entry 3161 (class 2606 OID 1494465)
-- Name: penjualan_detail penjualan_detail_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan_detail
    ADD CONSTRAINT penjualan_detail_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3157 (class 2606 OID 1494430)
-- Name: penjualan penjualan_kunjungan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan
    ADD CONSTRAINT penjualan_kunjungan_id_fkey FOREIGN KEY (kunjungan_id) REFERENCES public.kunjungan(id);


--
-- TOC entry 3159 (class 2606 OID 1494440)
-- Name: penjualan penjualan_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan
    ADD CONSTRAINT penjualan_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


--
-- TOC entry 3158 (class 2606 OID 1494435)
-- Name: penjualan penjualan_toko_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penjualan
    ADD CONSTRAINT penjualan_toko_id_fkey FOREIGN KEY (toko_id) REFERENCES public.toko(id);


--
-- TOC entry 3138 (class 2606 OID 1494177)
-- Name: produk produk_kategori_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT produk_kategori_id_fkey FOREIGN KEY (kategori_id) REFERENCES public.kategori_produk(id);


--
-- TOC entry 3167 (class 2606 OID 1494526)
-- Name: retur_detail retur_detail_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur_detail
    ADD CONSTRAINT retur_detail_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3166 (class 2606 OID 1494521)
-- Name: retur_detail retur_detail_retur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur_detail
    ADD CONSTRAINT retur_detail_retur_id_fkey FOREIGN KEY (retur_id) REFERENCES public.retur(id) ON DELETE CASCADE;


--
-- TOC entry 3163 (class 2606 OID 1494492)
-- Name: retur retur_kunjungan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur
    ADD CONSTRAINT retur_kunjungan_id_fkey FOREIGN KEY (kunjungan_id) REFERENCES public.kunjungan(id);


--
-- TOC entry 3165 (class 2606 OID 1494502)
-- Name: retur retur_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur
    ADD CONSTRAINT retur_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


--
-- TOC entry 3164 (class 2606 OID 1494497)
-- Name: retur retur_toko_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.retur
    ADD CONSTRAINT retur_toko_id_fkey FOREIGN KEY (toko_id) REFERENCES public.toko(id);


--
-- TOC entry 3170 (class 2606 OID 1494552)
-- Name: stok_expired_toko stok_expired_toko_penitipan_detail_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_expired_toko
    ADD CONSTRAINT stok_expired_toko_penitipan_detail_id_fkey FOREIGN KEY (penitipan_detail_id) REFERENCES public.penitipan_detail(id);


--
-- TOC entry 3169 (class 2606 OID 1494547)
-- Name: stok_expired_toko stok_expired_toko_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_expired_toko
    ADD CONSTRAINT stok_expired_toko_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3168 (class 2606 OID 1494542)
-- Name: stok_expired_toko stok_expired_toko_toko_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_expired_toko
    ADD CONSTRAINT stok_expired_toko_toko_id_fkey FOREIGN KEY (toko_id) REFERENCES public.toko(id);


--
-- TOC entry 3145 (class 2606 OID 1494284)
-- Name: stok_gudang stok_gudang_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_gudang
    ADD CONSTRAINT stok_gudang_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3172 (class 2606 OID 1494620)
-- Name: stok_sales stok_sales_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_sales
    ADD CONSTRAINT stok_sales_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3171 (class 2606 OID 1494615)
-- Name: stok_sales stok_sales_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_sales
    ADD CONSTRAINT stok_sales_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


--
-- TOC entry 3154 (class 2606 OID 1494381)
-- Name: stok_toko stok_toko_produk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_toko
    ADD CONSTRAINT stok_toko_produk_id_fkey FOREIGN KEY (produk_id) REFERENCES public.produk(id);


--
-- TOC entry 3153 (class 2606 OID 1494376)
-- Name: stok_toko stok_toko_toko_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stok_toko
    ADD CONSTRAINT stok_toko_toko_id_fkey FOREIGN KEY (toko_id) REFERENCES public.toko(id);


--
-- TOC entry 3140 (class 2606 OID 1494217)
-- Name: toko toko_sales_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.toko
    ADD CONSTRAINT toko_sales_id_fkey FOREIGN KEY (sales_id) REFERENCES public.users(id);


-- Completed on 2026-06-12 14:22:48

--
-- PostgreSQL database dump complete
--

