## arm1176jzf_s

---

### Modos operativos del procesador

En todos los estados existen ocho modos de operación:

1. Modo usuario (User mode -USR-)
2. Modo de atención de Solicitudes de Interrupción Rápida (Fast Interrupt Request -FIQ-)
3. Modo de atención de Solicitudes de Interrupción (Interrupt -IRQ-)
4. Modo supervisor (Supervisor mode -SVC-)
5. Modo de aborción (Abort mode -ABT-)
6. Modo de sistema (System mode -SYS-)
7. Modo de excepción para instrucciones no definidas (Undefined -UND-)
8. Modo de monitoreo seguro (Secure Monitor mode)

---

### Registros

El procesador tiene un total de 40 registros:

- 33 registros de propósito general de 32 bits
- 7 registros de estado de 32 bits

No todos son accesibles al mismo tiempo.
