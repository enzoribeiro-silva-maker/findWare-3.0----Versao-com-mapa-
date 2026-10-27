#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

const byte LED_AMARELO = 5;
const byte LED_VERMELHO = 6;
const byte LED_VERDE = 7;
const byte BUZZER = 8;
const byte RFID_SS = 2;
const byte RFID_RST = 3;

MFRC522 rfid(RFID_SS, RFID_RST);
LiquidCrystal_I2C lcd(0x27, 16, 2);
char comando[48];
byte indiceComando = 0;
String uidProcurado = "";
bool buscaAtiva = false;
bool leitorOK = false;

void mostrarLCD(const char* a, const char* b) {
  lcd.clear(); lcd.setCursor(0, 0); lcd.print(a);
  lcd.setCursor(0, 1); lcd.print(b);
}
void ledsOff() {
  digitalWrite(LED_AMARELO, LOW); digitalWrite(LED_VERMELHO, LOW); digitalWrite(LED_VERDE, LOW);
}
String obterUID() {
  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase(); return uid;
}
void processarComando(char* texto) {
  String msg = String(texto); msg.trim();
  if (msg == "PING") {
    Serial.println("PONG");
  } else if (msg.startsWith("BUSCAR:")) {
    uidProcurado = msg.substring(7); uidProcurado.trim(); uidProcurado.toUpperCase();
    if (!uidProcurado.length()) { Serial.println("ERRO:UID vazio"); return; }
    buscaAtiva = true; ledsOff(); digitalWrite(LED_VERDE, HIGH); noTone(BUZZER);
    mostrarLCD("BUSCA FINDWARE", "Aproxime a tag");
    Serial.print("BUSCA_INICIADA:"); Serial.println(uidProcurado);
  } else if (msg == "PARAR") {
    buscaAtiva = false; uidProcurado = ""; noTone(BUZZER); ledsOff(); digitalWrite(LED_VERDE, HIGH);
    mostrarLCD("FINDWARE IoT", "Busca parada"); Serial.println("BUSCA_PARADA");
  } else {
    Serial.print("ERRO:Comando desconhecido: "); Serial.println(msg);
  }
}
void lerSerial() {
  while (Serial.available() > 0) {
    char c = (char)Serial.read();
    if (c == '\n' || c == '\r') {
      if (indiceComando > 0) { comando[indiceComando] = '\0'; processarComando(comando); indiceComando = 0; }
    } else if (indiceComando < sizeof(comando) - 1) {
      comando[indiceComando++] = c;
    } else { indiceComando = 0; Serial.println("ERRO:Comando muito longo"); }
  }
}
void setup() {
  Serial.begin(9600);
  pinMode(LED_AMARELO, OUTPUT); pinMode(LED_VERMELHO, OUTPUT); pinMode(LED_VERDE, OUTPUT); pinMode(BUZZER, OUTPUT);
  ledsOff(); noTone(BUZZER); Wire.begin(); lcd.init(); lcd.backlight();
  mostrarLCD("FINDWARE IoT", "Iniciando..."); SPI.begin(); rfid.PCD_Init(); delay(500);
  byte versao = rfid.PCD_ReadRegister(MFRC522::VersionReg);
  leitorOK = !(versao == 0x00 || versao == 0xFF);
  if (!leitorOK) {
    digitalWrite(LED_AMARELO, HIGH); mostrarLCD("ERRO NO RFID", "Verifique os fios");
    Serial.println("ERRO:Leitor RFID sem comunicacao"); return;
  }
  digitalWrite(LED_VERDE, HIGH); mostrarLCD("FINDWARE IoT", "Aguardando PC"); Serial.println("FINDWARE_PRONTO");
}
void loop() {
  lerSerial(); if (!leitorOK) return;
  if (!rfid.PICC_IsNewCardPresent()) return;
  if (!rfid.PICC_ReadCardSerial()) return;
  String uid = obterUID(); Serial.print("UID_LIDO:"); Serial.println(uid);
  if (buscaAtiva && uid == uidProcurado) {
    buscaAtiva = false; uidProcurado = ""; ledsOff(); digitalWrite(LED_VERMELHO, HIGH);
    tone(BUZZER, 1500, 700); mostrarLCD("PRODUTO", "ENCONTRADO!");
    Serial.print("ENCONTRADO:"); Serial.println(uid);
  } else if (buscaAtiva) {
    digitalWrite(LED_AMARELO, HIGH); digitalWrite(LED_VERDE, LOW); digitalWrite(LED_VERMELHO, LOW);
    mostrarLCD("Tag diferente", "Continue buscando"); Serial.print("NAO_ENCONTRADO:"); Serial.println(uid);
  }
  rfid.PICC_HaltA(); rfid.PCD_StopCrypto1(); delay(250);
}
