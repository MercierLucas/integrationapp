//**************************************************************************************
//**************************************************************************************
//  Ce programme permet de tester rapidement l'envoi 
//  d'une trame courante vers la passerelle
//  Vous devez completer ce programme avec votre code pour qu'il soit complet     ****
//**************************************************************************************
//**************************************************************************************

#define SIZE_ENVOI 17
#define SIZE_RECEP 15

#define LED RED_LED

#define T1 11   // entrée T1 du hacheur pour contrôller le moteur
#define T2 38   // entrée T2 du hacheur pour contrôller le moteur

#define inputVentilo 12

#define ValTemp 7
#define ValDist 24
#define ValLum 25

// ajouter les #define pour les numéros de Pin.
// ajouter les variables globales pour la clé sonore

char  Conv_hexToAsc(char digH);         // définition de la fonction de onversion d'un chiffre hexa en code ASCII
void  Envoi_Trame(int valcapt, int typeCapt); // définition de la fonction d'envoi d'une trame
void  Recep_Trame(void);      // définition de la fonction de réception d'une trame
void  Wait_CleSonore(void);           // fonction qui attend la clé sonore
char  TrameEnvoi[20];                   // buffer pour envoyer  une trame vers la passerelle
char  TrameRecep[20];                   // buffer pour recevoir une trame venant de la passerelle
char  TrameFinale[14];
char  CheckSum;
int  i,disp;
String  valcaptrecep,typecaptrecep;

void setup()
{
  // put your setup code here, to run once:
  Serial.begin(9600);
  Serial1.begin(9600);

  pinMode(inputVentilo, OUTPUT);
  pinMode(T1, OUTPUT);
  pinMode(T2, OUTPUT);
  pinMode(LED, OUTPUT);  
  pinMode(inputVentilo, OUTPUT);  
        
  // ajouter ici les autres initialisations
  //  -- code du setup() pour la clé sonore

  // Partie constante de la trame 

  TrameEnvoi[0] = '1';  // le champ TRA. choisir toujours "Trame courante = 1"

  // le champ OBJ (4 octets) = numero de groupe. ex 010A 010B ...

  TrameEnvoi[1] = '0';  // mettre le chiffre du numero de groupe (0)
  TrameEnvoi[2] = '0';  // mettre le chiffre du numero de groupe (1)
  TrameEnvoi[3] = '9';  // mettre le chiffre du numero de groupe (0)
  TrameEnvoi[4] = 'D';  // mettre la lettre  du numero de groupe (A, B, ... E)
  TrameEnvoi[5] = '1';  // champ REQ. 1= Requete en ecriture

//  TrameEnvoi[6] = ;   // champ TYP. remplir dans la boucle (voir Doc)

  TrameEnvoi[7] = '0';  // champ NUM (2 octets). Numero du capteur
  TrameEnvoi[8] = '1';  // On choisit par exemple le numero 01.

//  TrameEnvoi[ 9] = ;    // Champ VAL (4 octets) = valeur à envoyer au site web
//  TrameEnvoi[10] = ;    // par exemple la valeur du capteur de lumiere
//  TrameEnvoi[11] = ;
//  TrameEnvoi[12] = ;

  TrameEnvoi[13] = 'B'; // Champ TIM (4 octets) = heure d'envoi de la trame
  TrameEnvoi[14] = 'A'; // Ce champ n'est pas utilisé par la passerelle; donc
  TrameEnvoi[15] = 'B'; // on peut mettre la valeur qu'on veut
  TrameEnvoi[16] = 'A';

//  TrameEnvoi[17] = ;    // premier  chiffre (poid fort)   du checksum
//  TrameEnvoi[18] = ;    // deuxieme chiffre (poid faible) du checksum
}



void loop()
{
  int n, valcapt, typeCapt;   // valeur lue du capteur
  int time = 0;
  String Smin, Ssec;
  
  // put your main code here, to run repeatedly:
  // ajouter ici le test de la clé sonore
  
  //Wait_CleSonore(); 

  while (1) {
    if(time>=595){
      
      // lire ici le capteur 1 et mettre la valeur dans la variable valcapt
      valcapt = analogRead(ValDist);
      if(valcapt<1000){
        valcapt = 0;
      }else{
        valcapt = 1;
      }
      typeCapt = 0x31;  // mettre le code correspondant au capteur 1
      Envoi_Trame(valcapt, typeCapt);
      
      // lire ici le capteur 2 et mettre la valeur dans la variable valcapt
      valcapt = analogRead(ValTemp);
      typeCapt = 0x33;  // mettre le code correspondant au capteur 2
      Envoi_Trame(valcapt, typeCapt);
      
      // lire ici le capteur 3 et mettre la valeur dans la variable valcapt
      valcapt = analogRead(ValLum);
      typeCapt = 0x35;  // mettre le code correspondant au capteur 3
      Envoi_Trame(valcapt, typeCapt);
                
      time=0;
      
      Smin = "40";
      Ssec = "24";
                
      TrameEnvoi[13] = Smin[0]; // Champ TIM (4 octets) = heure d'envoi de la trame
      TrameEnvoi[14] = Smin[1]; // Ce champ n'est pas utilisé par la passerelle; donc
      TrameEnvoi[15] = Ssec[0]; // on peut mettre la valeur qu'on veut
      TrameEnvoi[16] = Ssec[1];

      // Lire la réponse de la passerelle
      //Recep_Trame();
      // analyser la réponse et décider s'il faut commander un actionneur
      
      /*
      analogWrite(LED, 10);   // turn the LED on (HIGH is the voltage level)
      delay(50);               // wait for a second
      analogWrite(LED, 0);    // turn the LED off by making the voltage LOW
      */ 
      time=0;
    }
    Recep_Trame();
                  
    if(disp==1) //&& TrameFinale[13]=='0' && TrameFinale[14]=='1'
    {
      typecaptrecep = "";
      typecaptrecep = typecaptrecep + TrameFinale[6];

      valcaptrecep = "";
      valcaptrecep = valcaptrecep + TrameFinale[9];
      valcaptrecep = valcaptrecep + TrameFinale[10];
      valcaptrecep = valcaptrecep + TrameFinale[11];
      valcaptrecep = valcaptrecep + TrameFinale[12];

      if(typecaptrecep == "6"){

        Serial.println("Trame reçue concernant le moteur");
        
        if(valcaptrecep == "0001"){
          Serial.println("Démarrage du moteur");
          digitalWrite(T1,HIGH);
          digitalWrite(T2,LOW);
        }
        if(valcaptrecep == "0000"){
          Serial.println("Arrêt du moteur");
          digitalWrite(T1,LOW);
          digitalWrite(T2,LOW);
        }
      }
      
      if(typecaptrecep == "7"){

        Serial.println("Trame reçue concernant le ventillateur");
        
        if(valcaptrecep == "0001"){
          Serial.println("Démarrage du ventillateur");
          digitalWrite(inputVentilo,HIGH);
        }
        if(valcaptrecep == "0000"){
          Serial.println("Arrêt du ventillateur");
          digitalWrite(inputVentilo,LOW);
        }
      }
      
      if(typecaptrecep == "8"){

        Serial.println("Trame reçue concernant la lumière");
        
        if(valcaptrecep == "0001"){
          Serial.println("LED on");
          analogWrite(LED, 10);
        }
        if(valcaptrecep == "0000"){
          Serial.println("LED off");
          analogWrite(LED, 0);
        }
      }
      
      
      
      Serial.println(typecaptrecep);
      Serial.println(valcaptrecep);
      disp=0;
    }          
     
    time++;
    delay(10);        
  }
}

//---------------------------------

void  Envoi_Trame(int valcapt, int typeCapt)
{ 
        int n;  char digH, digA;  // digit (4 bits) Hexa et Ascii
  TrameEnvoi[6] = typeCapt; // type capteur

  // convertir la valeur du capteur en 4 chiffres ASCII (poid fort en premier)
  // conversion du 1er chiffre (poid fort)

  digH = (valcapt >> 12) & 0x0F;  // >> signifie décalage de 12 bits vers la droite
  digA = Conv_hexToAsc(digH);
  TrameEnvoi[9] = digA;

  // conversion du 2e chiffre

  digH = (valcapt >> 8) & 0x0F; // décalage de 8 bits vers la droite
  digA = Conv_hexToAsc(digH);
  TrameEnvoi[10] = digA;

  // conversion du 3e chiffre

  digH = (valcapt >> 4) & 0x0F; // décalage de 4 bits vers la droite
  digA = Conv_hexToAsc(digH);
  TrameEnvoi[11] = digA;

  // conversion du 4e chiffre (poid faible)

  digH = valcapt & 0x0F;    // pas besoin de décalage. garder juste le dernier digit
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
  digH = (CheckSum >> 4) & 0x0F;  // poid fort du CheckSum
  digA = Conv_hexToAsc(digH);
  Serial.print(digA);       // envoi du poid fort
  Serial1.print(digA);
  digH = CheckSum & 0x0F;     // poid faible du CheckSum
  digA = Conv_hexToAsc(digH);
  Serial.print(digA);       // envoi du poid faible
  Serial1.print(digA);
  Serial.println();       // retour à la ligne
}



//---------------------------------

char  Conv_hexToAsc(char digH)
{ 
        char valAsc;
  digH &= 0x0F;   // garder que les 4 bits de poid faible = 1 chiffre hexa (0 à 15)
  valAsc = digH + 0x30;
  if (digH > 9)
    valAsc += 0x07;
  return valAsc;
}

void  Recep_Trame(void)
{
  int countTrame;
  int rvdVal=Serial1.read();

  digitalWrite(inputVentilo,rvdVal);
  /*
  if(rvdVal!=-1){
    Serial.print("VALUE RECEIVED:");
    Serial.println(rvdVal);
  }
  */
  if(countTrame<SIZE_RECEP){
    TrameRecep[countTrame]=rvdVal;
    countTrame++;
  }else{

      // reset de la trame
      
      for (int n = 0; n < SIZE_RECEP; n++) {
        TrameRecep[n]=0;
      }
      countTrame=0;
      
      // traitement

      // Récupération du 1er bit de la nouvelle trame
      TrameRecep[0]=rvdVal;
      countTrame++;
  }
  if(rvdVal!=-1){
  /*
  Serial.print("Received trame = ");
  // display trame
  for (int n = 0; n < SIZE_RECEP; n++) {
    Serial.print(TrameRecep[n]);
    
  }
  */
  
  TrameFinale[i] = TrameRecep[0];

  if(i>=14){
    i=0;
    disp = 1;
    Serial.print(TrameFinale);
    Serial.println("");
  }else{
    i++;
  }
  
  }else{
    i=0;
  }
}

void  Wait_CleSonore(void)
{
  // Copier ici le code de test de la clé sonore

}
