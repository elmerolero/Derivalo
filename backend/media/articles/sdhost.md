

# SD Host (bajo revisión)

Parece ser que la Raspberry Pi 1A+, a diferencia de sus modelos posteriores utiliza un SD Host, mismo que documentaré aquí ya que no existe en la [documentación del BCM2835](https://www.raspberrypi.org/app/uploads/2012/02/BCM2835-ARM-Peripherals.pdf). Obtenido del [código fuente de Linux para Raspberry Pi](https://github.com/raspberrypi/linux/blob/rpi-6.12.y/drivers/mmc/host/bcm2835-sdhost.c) Es importante agregar que parte de esta información se está obteniendo con ayuda de inteligencia artificial (IA) ya que no parece existir una fuente específica para esta información más allá del código fuente. La realidad es que el driver de linux es bastante claro, pero igual para alguien nuevo, o alguien que quiere hacer lo suyo propio sin estar copiando le sería más util tenerlo documentado. Este documento es una descripción técnica independiente basada en el análisis del driver bcm2835-sdhost del kernel Linux. No se distribuye código fuente del kernel.


## Registers


| Dirección / Desplazamiento | Nombre del registro. | Descriptión | Tamaño |
| --- | --- | --- | --- |
| 0x00 | [SDCMD](#sdcmd) | Command to SD card. | 16  |
| 0x04 | [SDARG](#sdarg) | Argument to SD card. | 32  |
| 0x08 | [SDTOUT](#sdtout) | Start value for timeout counter. | 32  |
| 0x0C | [SDCDIV](#sdcdiv) | Start value for clock divider. | 11  |
| 0x10 | [SDRSP0](#sdrsp0) | SD Card response (31:0) | 32  |
| 0x14 | [SDRSP1](#sdrsp1) | SD Card response (63:32) | 32  |
| 0x18 | [SDRSP2](#sdrsp2) | SD Card response (95:64) | 32  |
| 0x1C | [SDRSP3](#sdrsp3) | SD Card response (127:96) | 32  |
| 0x20 | [SDHSTS](#sdhsts) | SD host status | 11  |
| 0x30 | [SDVDD](#sdvdd) | SD card power control | 32  |
| 0x34 | [SDEDM](#sdedm) | Emergency Debug Mode | 32  |
| 0x38 | [SDHCFG](#sdhcfg) | Host configuration | 2   |
| 0x3C | [SDHBCT](#sdhbct) | Host byte count | 32  |
| 0x40 | [SDDATA](#sddata) | Data to/from SD card | 32  |
| 0x50 | [SDHBLC](#sdhblc) | Host block count (SDIO/SDHC) | 9   |

### SDCMD register

| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:16 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 15  | NEW\_FLAG | Indicates that a new command will be issued. | R/W | 0   |
| 14  | FAIL\_FLAG | Indicates an error sending the command. | R/W | 0   |
| 13:12 | \-  | Reserved - Write as 0, read as do not care. | \-  | \-  |
| 11  | CMD\_BUSYWAIT | Indicates that the card may remain busy after the command response, and the host must wait until the card releases the DAT0 line | R/W | 0   |
| 10  | NO\_RESPONSE | Indicates that the command does not return a response. | R/W | 0   |
| 9   | LONG\_RESPONSE | Indicates that command returns a response of 136 bits. | R/W | 0   |
| 8   | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 7   | WRITE\_CMD | Indicates that the command involves data from the card to the host. | R/W | 0   |
| 6   | READ\_CMD | Indicates that the command involves data from the card to the host. | R/W | 0   |
| 5:0 | CMD | Command | R/W | 0   |

### SDARG register

Command's argument register.
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | ARGUMENT | Command's argument | R/W | 0   |


### SDTOUT register

Start value for timeout counter.
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | TOUT | Start value for timeout counter | R/W | 0   |


### SDCDIV register

Start value for clock divider.
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:12 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 11:0 | CDIV | Start value for timeout divider | R/W | 0   |


### SDRSP0 register

SD card response (31:0)
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | RESP0 | Response from 0 to 31 | R/W | 0   |



### SDRSP1 register


SD card response (63:32)
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | RESP1 | Response from 32 to 63 | R/W | 0   |

### SDRSP2 register

SD card response (95:64)
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | RESP2 | Response from 64 to 95 | R/W | 0   |

### SDRSP3 register

SD card response (127:96)
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | RESP3 | Response from 96 to 127 | R/W | 0   |


### SDHSTS register


SD Host status register. Write 1 for each bit that you want to clear.
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 10  | BUSY\_IRPT | Indicates that the card has released the busy state (DAT0 returned high). This flag is set when the card transitions from BUSY to NOT BUSY after a command. | R   | 0   |
| 9   | BLOCK\_IRPT | Indicates that a block boundary has been reached during a data transfer. This flag is set each time one data block is completely transferred. | R   | 0   |
| 8   | SDIO\_IRPT | Indicates that an SDIO interrupt was asserted by the card. This flag is set when the SDIO interrupt line from the card becomes active. | R   | 0   |
| 7   | REW\_TIME\_OUT | Indicates that a read or write data transfer did not complete within the programmed data timeout period. This flag is set when the card fails to complete a data read or write operation before the data timeout expires. | R   | 0   |
| 6   | CMD\_TIME\_OUT | Indicates that a command response was not received within the programmed timeout period. This flag is set when the card fails to respond to a command before the command timeout expires. | R   | 0   |
| 5   | CRC16\_ERROR | Indicates that a CRC16 error was detected during the data phase of a command. This flag is set when the CRC of received or transmitted data does not match the expected value. | R   | 0   |
| 4   | CRC7\_ERROR | Indicates that a CRC7 error was detected in the command response. This flag is set when the CRC of a received command response does not match the expected value. | R   | 0   |
| 3   | FIFO\_ERROR | Indicates that a FIFO overrun or underrun occurred during a data transfer. This flag is set when the host FIFO could not be serviced in time, causing loss or invalidation of data. | R   | 0   |
| 2:1 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 0   | DATA\_FLAG | Indicates that there is an active transfer. | R   | 0   |

### SDVDD register


SD Card Power Control register, use it to enable the SD Host module.
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:1 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 0   | PWRON | SD Host power on | R/W | 0   |

### SDEDM register


Emergency Debug Mode
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:22 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 21  | BYPASS | Bypasses the SDHOST internal data engine and FIFO, allowing direct interaction with the SD data lines. | R/W | 0   |
| 20  | CLOCK\_PULSE | Generates extra clock pulses on the SD clock line without issuing a command or transferring data. | R/W | 0   |
| 19  | FORCE\_DATA\_MODE | Forces the SDHOST data engine into data mode regardless of the current command state. | R/W | 0   |
| 18:14 | READ\_THRESHOLD | Defines the FIFO level at which the host requests additional data during a read transfer. | R/W | 0   |
| 13:9 | WRITE\_THRESHOLD | Defines the FIFO level at which the host requests additional data during a write transfer. | R/W | 0   |
| 8:4 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 3:0 | FSM | Finite State Machine. Indicates the current internal state of the SDHOST data engine finite state machine. It supports the following states:  <br>0000 - IDENTMODE  <br>0001 - DATAMODE  <br>0010 - READDATA  <br>0011 - WRITEDATA  <br>0100 - READWAIT  <br>0101 - READCRC  <br>0110 - WRITECRC  <br>0111 - WRITEWAIT1  <br>1000 - POWERDOWN  <br>1001 - POWERUP  <br>1010 - WRITESTART1  <br>1011 - WRITESTART2  <br>1100 - GENPULSES  <br>1101 - WRITEWAIT2  <br>1110 - Reserved  <br>1111 - STARTPOWDOWN | R   | 0   |

### SDHCFG register

| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:11 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 10  | BUSY\_IRPT\_EN | Enabling interrupts when DAT0 changes from BUSY to IDLE. | R/W | 0   |
| 9   | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 8   | BLOCK\_IRPT\_EN | Enabling interrupts when a block is completed. | R/W | 0   |
| 7:6 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 5   | SDIO\_IRPT\_ENABLE | Enables interrupt generation for SDIO modules when an interrupt is asserted on DAT1.  <br>It does not have effect for standard SD memory cards. | R/W | 0   |
| 4   | DATA\_IRPT\_EN | Enabling interrupts per data event. | R/W | 0   |
| 3   | SLOW\_CARD | Special settings for slow cards. | R/W | 0   |
| 2   | WIDE\_EXT\_BUS | Enables wider external system bus access to the SDHOST data path.  <br>This bit does not affect the SD card bus width. | R/W | 0   |
| 1   | SDHCFG\_WIDE\_INT\_BUS | Enables wider internal data handling withing the SDHOST controller.  <br>THis bit does no affect the SD bus width toward the card. | R/W | 0   |
| 0   | REL\_CMD\_LINE | Enables SD Host. | R/W | 0   |

  

### SDHBCT register


Host byte count. Specifies the total number of data bytes to be transferred by the host during a data command.
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | HBCT | Host byte count value | R/W | 0   |

  

### SDDATA register


Data to/from SD card
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:0 | DATA | Data from/to the card | R/W | 0   |


### SDBLC register


Host block count
| Bit(s) | Field name | Description | Type | Reset |
| --- | --- | --- | --- | --- |
| 31:9 | \-  | Reserved - Write as 0, read as do not care | \-  | \-  |
| 0:8 | HBLC | Host block count | R/W | 0   |
