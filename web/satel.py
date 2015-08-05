import serial
import time
import re

ser = serial.Serial('COM3')

ser.baudrate = '2400'
ser.bytesize = serial.SEVENBITS
ser.parity = serial.PARITY_ODD
#ser.timeout=1
ser.stopbits = serial.STOPBITS_TWO
ser.xonxoff=1

byte=27
ser.write(chr(byte))
ser.write('P')


respuesta = ser.readline()

respuesta = respuesta.replace("+","")
respuesta = respuesta.replace(" ","")
respuesta = respuesta.replace("g","")
respuesta = respuesta.replace('\r\n',"") 

repuesta = re.sub("\D","",respuesta)

print respuesta[-7:]
