



-- public.fac_tipos_tributos definition

-- Drop table

-- DROP TABLE public.fac_tipos_tributos;

CREATE TABLE public.fac_tipos_tributos (
	id serial4 NOT NULL,
	codigo varchar(10) NOT NULL,
	descripcion varchar(200) DEFAULT NULL::character varying NULL,
	CONSTRAINT fac_tipos_tributos_pkey PRIMARY KEY (id)
);

-- public.fac_montos_tributo_facturas definition

-- Drop table

-- DROP TABLE public.fac_montos_tributo_facturas;

CREATE TABLE public.fac_montos_tributo_facturas (
	id serial4 NOT NULL,
	monto numeric(10, 2) NOT NULL,
	id_factura int4 NOT NULL,
	id_tipo_tributo int4 NOT NULL,
	created_at timestamp NULL,
	updated_at timestamp NULL,
	CONSTRAINT fac_montos_tributo_facturas_pkey PRIMARY KEY (id)
);


-- public.fac_montos_tributo_facturas foreign keys

ALTER TABLE public.fac_montos_tributo_facturas ADD CONSTRAINT fac_montos_tributo_facturas_id_factura_fkey FOREIGN KEY (id_factura) REFERENCES public.factura_electronicas(id) ON UPDATE CASCADE;
ALTER TABLE public.fac_montos_tributo_facturas ADD CONSTRAINT fac_montos_tributo_facturas_id_tipo_tributo_fkey FOREIGN KEY (id_tipo_tributo) REFERENCES public.fac_tipos_tributos(id) ON UPDATE CASCADE;

-- public.factura_electronicas definition

-- Drop table

-- DROP TABLE public.factura_electronicas;

CREATE TABLE public.factura_electronicas (
	id serial4 NOT NULL,
	"codigoGeneracion" varchar(100) NULL,
	"nombreComercial" varchar(130) NULL,
	nit varchar(15) NULL,
	telefono varchar(15) NULL,
	"totalPagar" float4 NULL,
	updated_at timestamp NULL,
	created_at timestamp NULL,
	pdf varchar(150) NULL,
	"json" varchar NULL,
	"fechaEmision" date NULL,
	direccion_origen varchar(50) NULL,
	fecha_correo timestamp NULL,
	id_user int4 NULL,
	"fechaAsignacion" timestamp NULL,
	id_user_tesoreria int4 NULL,
	"fechaLiquidacion" timestamp NULL,
	id_area int4 NULL,
	id_norma int4 NULL,
	descripcion varchar(400) NULL,
	id_user_process int4 NULL,
	codigo_sap varchar(30) NULL,
	id_user_revision_5 int4 NULL,
	fecha_revision_5 timestamp NULL,
	json_tributos varchar(550) NULL,
	inactiva bool NULL,
	fecha_inactiva timestamp NULL,
	id_user_inactiva int4 NULL,
	sello varchar(150) NULL,
	num_registro_diario varchar(20) NULL,
	num_entrada_mercaderia varchar(25) NULL,
	tipo_dte varchar(3) NULL,
	message_id varchar(120) NULL,
	local_sello varchar(50) NULL,
	local_codigo varchar(40) NULL,
	local_asiento_diario varchar(10) NULL,
	json_interno varchar(31) NULL,
	pdf_interno varchar(31) NULL,
	set_entrada_merc int4 NULL,
	iva_percibido float8 NULL,
	valor_operaciones float8 NULL,
	monto_sujeto_percepcion float8 NULL,
	numero_control varchar(35) NULL,
	nit_receptor varchar(20) NULL,
	CONSTRAINT factura_electronicas_pkey PRIMARY KEY (id),
	CONSTRAINT unique_codigo_generacion UNIQUE ("codigoGeneracion")
);


-- public.factura_electronicas foreign keys

ALTER TABLE public.factura_electronicas ADD CONSTRAINT factura_electronicas_fk FOREIGN KEY (id_area) REFERENCES public.fac_areas(id) ON UPDATE CASCADE;
ALTER TABLE public.factura_electronicas ADD CONSTRAINT factura_electronicas_fk_1 FOREIGN KEY (id_norma) REFERENCES public.fac_norma_repartos(id) ON UPDATE CASCADE;
ALTER TABLE public.factura_electronicas ADD CONSTRAINT factura_electronicas_reg_entrada FOREIGN KEY (set_entrada_merc) REFERENCES public.users(id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- public.fac_norma_repartos definition

-- Drop table

-- DROP TABLE public.fac_norma_repartos;

CREATE TABLE public.fac_norma_repartos (
	id serial4 NOT NULL,
	nombre varchar(150) NOT NULL,
	codigo varchar(10) NOT NULL,
	CONSTRAINT fac_norma_repartos_pkey PRIMARY KEY (id)
);

-- public.fac_sujeto_excluido definition

-- Drop table

-- DROP TABLE public.fac_sujeto_excluido;

CREATE TABLE public.fac_sujeto_excluido (
	id serial4 NOT NULL,
	num_entrada_sap varchar(10) DEFAULT NULL::character varying NULL,
	id_factura_electronica int4 NOT NULL,
	estado int4 NOT NULL,
	created_at timestamp NOT NULL,
	updated_at timestamp NULL,
	id_norma_reparto_sap int4 NULL,
	id_user_asignado int4 NULL,
	fecha_asignacion timestamp NULL,
	marca_recibido bool DEFAULT false NULL,
	id_usuario int4 NULL,
	set_estado_5 int4 NULL,
	set_estado_6 int4 NULL,
	CONSTRAINT fac_sujeto_excluido_pkey PRIMARY KEY (id)
);


-- public.fac_sujeto_excluido foreign keys

ALTER TABLE public.fac_sujeto_excluido ADD CONSTRAINT fac_sujeto_excluido_fk FOREIGN KEY (id_usuario) REFERENCES public.users(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE public.fac_sujeto_excluido ADD CONSTRAINT fac_sujeto_excluido_id_factura_electronica_fkey FOREIGN KEY (id_factura_electronica) REFERENCES public.factura_electronicas(id) ON UPDATE CASCADE;
ALTER TABLE public.fac_sujeto_excluido ADD CONSTRAINT fac_sujeto_excluido_id_norma_reparto_sap_fkey FOREIGN KEY (id_norma_reparto_sap) REFERENCES public.fac_norma_repartos(id);




