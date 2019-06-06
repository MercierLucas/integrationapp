//**************************************************************************************
//**************************************************************************************
//  Ce programme permet de tester rapidement l'envoi 
//	d'une trame courante vers la passerelle
//  Vous devez completer ce programme avec votre code pour qu'il soit complet    	****
//**************************************************************************************
//**************************************************************************************
#define		SIZE_ENVOI	17
#define		SIZE_RECEP	15
// ajouter les #define pour les numéros de Pin.
// ajouter les variables globales pour la clé sonore

char	Conv_hexToAsc(char digH);	// définition de la fonction de ...
									// conversion d'un chiffre hexa en code ASCII
void	Envoi_Trame(int valcapt, int typeCapt);	// définition de la fonction d'envoi ...
												// d'une trame
void	Recep_Trame(void);			// définition de la fonction de réception d'une trame
void	Wait_CleSonore(void);		// fonction qui attend la clé sonore

char	TrameEnvoi[20];		// buffer pour envoyer  une trame vers la passerelle
char	TrameRecep[20];		// buffer pour recevoir une trame venant de la passerelle
char	CheckSum;

void setup()
{
	// put your setup code here, to run once:
	Serial.begin(9600);
	Serial1.begin(9600);

	// ajouter ici les autres initialisations
	//	-- code du setup() pour la clé sonore

	// Partie constante de la trame 
	TrameEnvoi[0] = '1';	// le champ TRA. choisir toujours "Trame courante = 1"
							// le champ OBJ (4 octets) = numero de groupe. ex 010A 010B ...
	TrameEnvoi[1] = '0';	// mettre le chiffre du numero de groupe (0)
	TrameEnvoi[2] = '0';	// mettre le chiffre du numero de groupe (1)
	TrameEnvoi[3] = '9';	// mettre le chiffre du numero de groupe (0)
	TrameEnvoi[4] = 'D';	// mettre la lettre  du numero de groupe (A, B, ... E)
	TrameEnvoi[5] = '1';	// champ REQ. 1= Requete en ecriture
//	TrameEnvoi[6] = ;		// champ TYP. remplir dans la boucle (voir Doc)
	TrameEnvoi[7] = '0';	// champ NUM (2 octets). Numero du capteur
	TrameEnvoi[8] = '1';	// On choisit par exemple le numero 01.
//	TrameEnvoi[ 9] = ;		// Champ VAL (4 octets) = valeur à envoyer au site web
//	TrameEnvoi[10] = ;		// par exemple la valeur du capteur de lumiere
//	TrameEnvoi[11] = ;
//	TrameEnvoi[12] = ;
	TrameEnvoi[13] = 'B';	// Champ TIM (4 octets) = heure d'envoi de la trame
	TrameEnvoi[14] = 'A';	// Ce champ n'est pas utilisé par la passerelle; donc
	TrameEnvoi[15] = 'B';	// on peut mettre la valeur qu'on veut
	TrameEnvoi[16] = 'A';
//	TrameEnvoi[17] = ;		// premier  chiffre (poid fort)   du checksum
//	TrameEnvoi[18] = ;		// deuxieme chiffre (poid faible) du checksum
}

void loop()
{
	int n, valcapt, typeCapt;		// valeur lue du capteur

	// put your main code here, to run repeatedly:

	// ajouter ici le test de la clé sonore
	//Wait_CleSonore(); 

	while (1) {
		valcapt = 0x1234;
		// lire ici le capteur 1 et mettre la valeur dans la variable valcapt
		//valcapt = .....;
		typeCapt = 0x31;	// mettre le code correspondant au capteur 1
		Envoi_Trame(valcapt, typeCapt);

		// lire ici le capteur 2 et mettre la valeur dans la variable valcapt
		//valcapt = .....;
		//typeCapt = 0x32;	// mettre le code correspondant au capteur 1
		//Envoi_Trame(valcapt, typeCapt);

		// Lire la réponse de la passerelle
		//Recep_Trame();
		// analyser la réponse et décider s'il faut commander un actionneur

		delay(5000);	// tempo de 5 secondes
	}
}

//---------------------------------
void	Envoi_Trame(int valcapt, int typeCapt)
//---------------------------------
{	int n;	char digH, digA;	// digit (4 bits) Hexa et Ascii

	TrameEnvoi[6] = typeCapt;	// type capteur
	// convertir la valeur du capteur en 4 chiffres ASCII (poid fort en premier)
	// conversion du 1er chiffre (poid fort)
	digH = (valcapt >> 12) & 0x0F;	// >> signifie décalage de 12 bits vers la droite
	digA = Conv_hexToAsc(digH);
	TrameEnvoi[9] = digA;
	// conversion du 2e chiffre
	digH = (valcapt >> 8) & 0x0F;	// décalage de 8 bits vers la droite
	digA = Conv_hexToAsc(digH);
	TrameEnvoi[10] = digA;
	// conversion du 3e chiffre
	digH = (valcapt >> 4) & 0x0F;	// décalage de 4 bits vers la droite
	digA = Conv_hexToAsc(digH);
	TrameEnvoi[11] = digA;
	// conversion du 4e chiffre (poid faible)
	digH = valcapt & 0x0F;		// pas besoin de décalage. garder juste le dernier digit
	digA = Conv_hexToAsc(digH);
	TrameEnvoi[12] = digA;

	Serial.print("Trame = ");
	// boucle pour envoyer une trame vers la passerelle
	CheckSum = 0;
	// envoi des 'SIZE_ENVOI' premiers octets
	for (n = 0; n < SIZE_ENVOI; n++) {
		Serial.print(TrameEnvoi[n]);
		Serial1.print(TrameEnvoi[n]);
		CheckSum = CheckSum + TrameEnvoi[n];
	}
	digH = (CheckSum >> 4) & 0x0F;	// poid fort du CheckSum
	digA = Conv_hexToAsc(digH);
	Serial.print(digA);				// envoi du poid fort
	Serial1.print(digA);
	digH = CheckSum & 0x0F;			// poid faible du CheckSum
	digA = Conv_hexToAsc(digH);
	Serial.print(digA);				// envoi du poid faible
	Serial1.print(digA);
	Serial.println();				// retour à la ligne
}

//---------------------------------
char	Conv_hexToAsc(char digH)
//---------------------------------
{	char valAsc;

	digH &= 0x0F;		// garder que les 4 bits de poid faible = 1 chiffre hexa (0 à 15)
	valAsc = digH + 0x30;
	if (digH > 9)
		valAsc += 0x07;
	return valAsc;
}

void	Recep_Trame(void)
//---------------------------------
//---------------------------------
{
	// boucle d'attente de  "SIZE_RECEP" octets dans le buffer TrameRecep[]
	// avant chaque lecture d'un octet, vérifier que le port de réception 
	// contient un octet en attente.
}


void	Wait_CleSonore(void)
//---------------------------------
//---------------------------------
{
	// Copier ici le code de test de la clé sonore
}
