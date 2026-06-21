## CMD9 y tabla de CSD versión 1 y 2 (bajo revisión)

La tabla de CSD corresponde a Datos Específicos de la Tarjeta (por sus siglas en inglés **C**ard **S**pecífic **D**ata). Corresponde a la información que se obtiene al ejecutar el comando CMD9.  
La respuesta que da el comando es de 128 bits. Lo que significa la información se muestra a continuación. La versión 1 se utiliza para tarjetas de memoria con poca capacidad (menores o iguales a 2 gigabytes), para casi todas las que superan esta capacidad, lo más probable es que se utilice la versión 2. (Utilizo "casi todas" solo para evitar asegurar algo que podría no ser cierto)

### Tabla de CSD v1

| Desplazamiento | Nombre | Campo | Ancho (en bits) | Valor | Tipo de celda |
| --- | --- | --- | --- | --- | --- |
| \[127:126\] | CSD Structure | CSD\_STRUCTURE | 2 bits | 00 | R |
| \[125:120\] | Reservado | \- | 6 | 00 0000b | R |
| \[119:112\] | data read access-time-1 | TAAC | 8 | XXh | R |
| \[111:104\] | data read access-time-2 in CLK cycles | NSAC | 8 | XXh | R |
| \[103:96\] | max. data transfer rate | TRAN\_SPEED | 8 | 32h o 5Ah | R |
| \[95:84\] | card command classes | CCC | 12 | 01x110110101b | R |
| \[83:80\] | max. read data block length | READ\_BL\_LEN | 4 | Xh | R |
| \[79:79\] | partial blocks for read allowed | READ\_BL\_PARTIAL | 1 | 1b | R |
| \[78:78\] | write block misalignment | WRITE\_BLK\_MISALIGN | 1 | 1b | R |
| \[77:77\] | read block misalignment | READ\_BLK\_MISALIGN | 1 | 1b | R |
| \[76:76\] | DSR implemented | DSR\_IMP | 1 | 1b | R |
| \[75:74\] | Reservado | \- | 2 | 00b | R |
| \[73:62\] | Device size | C\_SIZE | 12 | xxxh | R |
| \[61:59\] | max. read current @VDD min | VDD\_R\_CURR\_MIN | 3 | xxxb | R |
| \[61:59\] | max. read current @VDD min | VDD\_R\_CURR\_MIN | 3 | xxxb | R |
| \[58:56\] | max. read current @VDD max | VDD\_R\_CURR\_MAX | 3 | xxxb | R |
| \[55:53\] | max. write current @VDD min | VDD\_W\_CURR\_MIN | 3 | xxxb | R |
| \[52:50\] | max. write current @VDD max | VDD\_W\_CURR\_MAX | 3 | xxxb | R |
| \[49:47\] | device size multiplier | C\_SIZE\_MULT | 3 | xxxb | R |
| \[46:46\] | erase single block enable | ERASE\_BLK\_EN | 1 | xb | R |
| \[45:39\] | erase sector size | SECTOR\_SIZE | 7 | xxxxxxxb | R |
| \[38:32\] | write protect group size | WP\_GRP\_SIZE | 7 | xxxxxxxb | R |
| \[31:31\] | write protect group enable | WP\_GRP\_ENABLE | 1 | xb | R |
| \[30:29\] | Reservado (Do not use) | \- | 2 | 00b | R |
| \[28:26\] | write speed factor | R2W\_FACTOR | 3 | xxxb | R |
| \[25:22\] | max. write data block length | WRITE\_BL\_LEN | 4 | xxxxb | R |
| \[21:21\] | partial blocks for write allowed | WRITE\_BL\_PARTIAL | 1 | xb | R |
| \[20:16\] | Reservado | \- | 5 | 00000b | R |
| \[15:15\] | File format group | FILE\_FORMAT\_GRP | 1 | xb | R/W (1) |
| \[14:14\] | copy flag | COPY | 1 | xb | R/W (1) |
| \[13:13\] | permanent write protection | PERM\_WRITE\_PROTECT | 1 | xb | R/W (1) |
| \[12:12\] | temporary write protection | TMP\_WRITE\_PROTECT | 1 | xb | R/W |
| \[11:10\] | File format | FILE\_FORMAT | 2 | xxb | R/W (1) |
| \[9:8\] | Reservado | \- | 2 | 00b | R/W |
| \[7:1\] | CRC | CRC | 7 | xxxxxxxb | R/W |
| \[0:0\] | not used, always '1' | \- | 1 | 1b | \- |

Para calcular la capacidad en bytes de una tarjeta de memoria que usa SDSC v1.0 es necesario extraer 3 campos:

-   READ\_BL\_LEN
-   C\_SIZE
-   C\_SIZE\_MULT

**Fórmula:**

capacidad = (2 ^ READ\_BL\_LEN) \* (2 ^ (C\_SIZE\_MULT + 2)) \* (C\_SIZE + 1)

  

### Tabla de CSD v2

| Desplazamiento | Nombre | Campo | Ancho (en bits) | Valor | Tipo de celda |
| --- | --- | --- | --- | --- | --- |
| \[127:126\] | CSD Structure | CSD\_STRUCTURE | 2 bits | 00 | R |
| \[125:120\] | Reservado | \- | 6 | 00 0000b | R |
| \[119:112\] | data read access-time | (TAAC) | 8 | 0Eh | R |
| \[111:104\] | data read access-time in CLK cycles (NCAS \* 100) | (NSAC) | 8 | 00h | R |
| \[103:96\] | max. data transfer rate | TRAN\_SPEED | 8 | 32h, 5Ah 0Bh or 2Bh | R |
| \[95:84\] | card command classes | CCC | 12 | 01x110110101b | R |
| \[83:80\] | max. read data block length | READ\_BL\_LEN | 4 | 9 | R |
| \[79:79\] | partial blocks for read allowed | READ\_BL\_PARTIAL | 1 | 0 | R |
| \[78:78\] | write block misalignment | WRITE\_BLK\_MISALIGN | 0 | 1 | R |
| \[77:77\] | read block misalignment | READ\_BLK\_MISALIGN | 1 | 0 | R |
| \[76:76\] | DSR implemented | DSR\_IMP | 1 | x | R |
| \[75:70\] | Reservado | \- | 6 | 00 0000b | R |
| \[69:48\] | Device size | C\_SIZE | 22 | xxxxxxh | R |
| \[47:47\] | Reservado |
| \[46:46\] | erase single block enable | ERASE\_BLK\_EN | 1 | 1 | R |
| \[45:39\] | erase sector size | SECTOR\_SIZE | 7 | 7Fh | R |
| \[38:32\] | write protect group size | WP\_GRP\_SIZE | 7 | 0000000b | R |
| \[31:31\] | write protect group enable | WP\_GRP\_ENABLE | 1 | 0 | R |
| \[30:29\] | Reservado (Do not use) | \- | 2 | 00b | R |
| \[28:26\] | write speed factor | R2W\_FACTOR | 3 | 010b | R |
| \[25:22\] | max. write data block length | WRITE\_BL\_LEN | 4 | 9 | R |
| \[21:21\] | partial blocks for write allowed | WRITE\_BL\_PARTIAL | 1 | 0 | R |
| \[20:16\] | Reservado | \- | 5 | 00000b | R |
| \[15:15\] | File format group | FILE\_FORMAT\_GRP | 1 | 0b | R/W (1) |
| \[14:14\] | copy flag | COPY | 1 | x | R/W (1) |
| \[13:13\] | permanent write protection | PERM\_WRITE\_PROTECT | 1 | x | R/W (1) |
| \[12:12\] | temporary write protection | TMP\_WRITE\_PROTECT | 1 | x | R/W |
| \[11:10\] | File format | FILE\_FORMAT | 2 | 00b | R/W (1) |
| \[9:8\] | Reservado | \- | 2 | 00b | R/W |
| \[7:1\] | CRC | CRC | 7 | xxxxxxxb | R/W |
| \[0:0\] | not used, always '1' | \- | 1 | 1b | \- |

Para calcular la capacidad en bytes de una tarjeta de memoria que usa SDSC v2.0 es necesario extraer 3 campos:

-   C\_SIZE

**Fórmula:**

capacidad = (C\_SIZE + 1) \* 512 \* 1024;
