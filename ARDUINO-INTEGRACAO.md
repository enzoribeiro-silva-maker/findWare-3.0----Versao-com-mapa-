# Integração FindWare + Arduino

Foi adicionada a página `arduino` ao menu lateral e ao painel principal. A página usa a Web Serial API do navegador para comunicar com o Arduino pela porta USB, a 9600 baud.

## Firmware incluído

Arquivo: `arduino/FindWare_Arduino.ino`

Bibliotecas da IDE Arduino: `MFRC522` e `LiquidCrystal_I2C` (além de `SPI` e `Wire`, que acompanham a IDE).

## Protocolo serial

- Página -> Arduino: `PING`, `BUSCAR:<UID>`, `PARAR` (cada comando termina em nova linha).
- Arduino -> Página: `PONG`, `FINDWARE_PRONTO`, `UID_LIDO:<UID>`, `BUSCA_INICIADA:<UID>`, `ENCONTRADO:<UID>`, `NAO_ENCONTRADO:<UID>` ou `ERRO:...`.
- Baud rate: 9600.

## Requisitos

- Chrome ou Edge atualizado com suporte a Web Serial.
- Abrir o sistema por `localhost` ou outro contexto seguro compatível.
- Fechar o Monitor Serial da IDE Arduino antes de conectar a página, pois a porta serial não pode ser usada por dois programas ao mesmo tempo.
- RC522 alimentado com 3,3 V, não 5 V.

A página não consegue confirmar a comunicação real enquanto o Arduino não estiver conectado e executando o firmware compatível.
