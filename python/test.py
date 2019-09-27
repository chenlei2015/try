#!/usr/bin/python
# -*- coding: UTF-8 -*-
# 文件名：test.py

# 第一个注释
# print ("11")
#import winsound
#winsound.Beep(600,1000)

#import easygui
#easygui.msgbox('Hello World')

#引入库
import pyaudio
import wave
import sys

#定义数据流块
chunk = 1024

#只读方式打开wav文件
f = wave.open(r"./8868.wav","rb")
#f = wave.open(r"./4251.mp3","rb")

p = pyaudio.PyAudio()

#打开数据流
stream = p.open(format = p.get_format_from_width(f.getsampwidth()),
                channels = f.getnchannels(),
                rate = f.getframerate(),
                output = True)

#读取数据
data = f.readframes(chunk)

#播放
while data !="":
    stream.write(data)
    data = f.readframes(chunk)

#停止数据流
stream.stop_stream()
stream.close()

#关闭 PyAudio
p.terminate()






