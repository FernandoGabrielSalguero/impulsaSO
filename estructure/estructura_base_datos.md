📚 Estructura completa de la base de datos: u104036906_gestionImpulsa
📄 Tabla: correos_log
Columna	Tipo	Nulo	Clave	Default	Extra
id	int(10) unsigned	NO	PRI		auto_increment
user_auth_id	int(10) unsigned	YES	MUL		
correo	varchar(255)	NO			
asunto	varchar(255)	NO			
template	varchar(100)	YES			
mensaje_html	longtext	YES			
mensaje_text	text	YES			
estado	enum('enviado','fallido')	NO	MUL	fallido	
error	text	YES			
meta	longtext	YES			
created_at	timestamp	NO		current_timestamp()	

📄 Tabla: user_auth
Columna	Tipo	Nulo	Clave	Default	Extra
id	int(10) unsigned	NO	PRI		auto_increment
correo	varchar(255)	NO	UNI		
password	varchar(255)	NO			
rol	enum('impulsa_administrador','impulsa_emprendedor')	NO		impulsa_emprendedor	
verification_token	varchar(100)	YES			
email_verified_at	timestamp	YES			
created_at	timestamp	NO		current_timestamp()	
updated_at	timestamp	NO		current_timestamp()	on update current_timestamp()

📄 Tabla: user_contacto
Columna	Tipo	Nulo	Clave	Default	Extra
id	int(10) unsigned	NO	PRI		auto_increment
user_auth_id	int(10) unsigned	NO	UNI		
correo	varchar(255)	NO			
check_correo	tinyint(1)	NO		0	
permison_correo	tinyint(1)	NO		1	
whatsapp	varchar(30)	YES			
check_whatsapp	tinyint(1)	NO		0	
permison_whatsapp	tinyint(1)	NO		1	
created_at	timestamp	NO		current_timestamp()	
updated_at	timestamp	NO		current_timestamp()	on update current_timestamp()

🔗 Relaciones:
Columna user_auth_id referencia a user_auth.id
📄 Tabla: user_info
Columna	Tipo	Nulo	Clave	Default	Extra
id	int(10) unsigned	NO	PRI		auto_increment
user_auth_id	int(10) unsigned	NO	UNI		
nombre	varchar(100)	YES			
apellido	varchar(100)	YES			
apodo	varchar(100)	YES			
fecha_nacimiento	date	YES			
created_at	timestamp	NO		current_timestamp()	
updated_at	timestamp	NO		current_timestamp()	on update current_timestamp()

🔗 Relaciones:
Columna user_auth_id referencia a user_auth.id