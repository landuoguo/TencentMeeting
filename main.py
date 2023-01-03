# Auther:landuoguo

import json
import os
import random
import time
import re
import requests
import pyautogui
import win32api
import win32con
import win32gui
from PIL import ImageGrab
import pandas as pd
import pytesseract

y_list =[32,152,273,394,515,635]#x轴裁切坐标
x_list =[2,216,431,646,861,1075]#y轴裁切坐标

path_t = "c01-00/"#截图保存的目录
e_path = 'data.xlsx'#数据文件的名称
delay_time = 7.5 #检测间隔

#判断文件夹是否存在
if not os.path.exists(path_t):
    os.makedirs(path_t)
if not os.path.exists(path_t+"capture/"):
    os.makedirs(path_t+"capture/")
#判断数据文件是否存在
if not os.path.exists(path_t+'data.xlsx'):
    dff = pd.DataFrame(columns=["sit","ocr_text","cap_time"])
    dff.to_excel(path_t+'data.xlsx')

hwnd_title = {}
def get_all_hwnd(hwnd, mouse):
    if (win32gui.IsWindow(hwnd) and
            win32gui.IsWindowEnabled(hwnd) and
            win32gui.IsWindowVisible(hwnd)):
        hwnd_title.update({hwnd: win32gui.GetWindowText(hwnd)})

def setforeground_window(window_handle):
    while True:
        try:
            win32gui.SetForegroundWindow(window_handle)  # 强制在最前端显示
            return
        except:
            time.sleep(0.1)

# 查找所有窗口标题和句柄
win32gui.EnumWindows(get_all_hwnd, 0)
for h, t in hwnd_title.items():
    if t != '腾讯会议':
        continue
    if win32gui.GetClassName(h) != "TXGuiFoundation":  # 跳过其他窗口捕获主窗口
        continue
    print("获取到窗口句柄 " + str(h))
    ding_main_window_handle = h
    break

try:
    if ding_main_window_handle is None:
        exit(0)
except:
    print("请先打开窗口")
    os.system("pause")
    exit(0)


#win32gui.ShowWindow(ding_main_window_handle, win32con.SW_MAXIMIZE)  # 最大化
#setforeground_window(ding_main_window_handle)  # 强制在最前端显示
time.sleep(2)
ding_chrome_window=ding_main_window_handle
print("准备就绪，3s后开始检测")

#写入excel
def write_to_excel(l_sit,l_text,l_time):
    original_data = pd.read_excel(path_t+e_path)
    sit,ocr_text,cap_time = [], [], []
    for i in original_data.values:
        sit.append(i[1])
        ocr_text.append(i[2])
        cap_time.append(i[3])
    
    sit += l_sit
    ocr_text += l_text
    cap_time += l_time
    data2 = {
        'sit': sit,
        'ocr_text': ocr_text,
        'cap_time':cap_time
        }
    #print(data2)
    data2 = pd.DataFrame(data2)
    data2.to_excel(path_t+e_path)
#上传数据至服务器，可自行选用
def upload_to_server(l_sit,l_xtime,jsq):
    
    payload={'uid':l_sit,'xtime':l_xtime,'s':"*********",'j':jsq}#s为密钥
    url="https://***********/compo/get_upload.php?d="+json.dumps(payload)#改为自己的服务器地址
    res = requests.get(url)
    print(res)
    print(res.text)

#生成随机字符串
def get_random_str(randomlength=16):
  random_str =''
  base_str ='ABCDEFGHIGKLMNOPQRSTUVWXYZabcdefghigklmnopqrstuvwxyz0123456789'
  length =len(base_str) -1
  for i in range(randomlength):
    random_str +=base_str[random.randint(0, length)]
  return random_str
#裁切每个人的图像并进行判断
def image_cut2(img, left, upper, right, lower):
    box = (left, upper, right, lower)
    roi = img.crop(box)
    r,g,b = roi.getpixel((102, 95))#获取像素点颜色
    if r>=43 and r<=47 and g>=46 and g<=50 and b>=49 and b<=53:
        d = image_ocr(roi)
        roi.save(d['path']+'.jpg')
        return{'code':2,'sit':d['sit'],'ocr_text':d['ocr_text'],'cap_time':d['cap_time'],'x_time':d['x_time']}
    else:
        return{'code':1}
#ocr识别异常学生的座号
def image_ocr(img):
    box = (0,96,86,121)
    roi = img.crop(box)
    text=pytesseract.image_to_string(roi,lang='chi_sim')
    #print(text)
    is_student = "号" in text
    o_time = time.strftime("%Y-%m-%d %H-%M-%S")
    x_time = time.strftime("%Y-%m-%d %H:%M:%S")
    if is_student:
        sit= re.findall('[0-9]+\号', text)[0].strip('号')
        out_path = path_t+sit+"/"+o_time
        if not os.path.exists(path_t+sit+"/"):
            os.makedirs(path_t+sit+"/")
    else:
        out_path = path_t+"err/"+o_time+get_random_str()
        sit = -1
        if not os.path.exists(path_t+"err/"):
            os.makedirs(path_t+"err/")
    print(sit)
    return {"path":out_path,'sit':int(sit),'ocr_text':text,'cap_time':o_time,'x_time':x_time}


while True:
    #win32gui.ShowWindow(ding_main_window_handle, win32con.SW_MAXIMIZE)  # 最大化
    #setforeground_window(ding_main_window_handle)  # 强制在最前端显示
    time.sleep(0.3)
    x_start, y_start, x_end, y_end = win32gui.GetWindowRect(ding_chrome_window)
    box = (x_start, y_start, x_end, y_end)
    image = ImageGrab.grab(box)

    
    with open(path_t+"record.txt", "a") as file:
        file.write(time.strftime('%m-%d %H-%M-%S') + "\n")
    #win32gui.ShowWindow(ding_main_window_handle, win32con.SW_MINIMIZE)  # 截图完成后最小化

    image.save(path_t+'capture/'+time.strftime('%m-%d %H-%M-%S')+'.jpg')#保存截图备用
    
    l_sit = []
    l_sit2 = []
    l_text = []
    l_time = []
    l_xtime = []
    jsq=0

    for i1 in range(0,5):
        for i2 in range(0,5):
            left, upper, right, lower = x_list[i1], y_list[i2], x_list[i1+1], y_list[i2+1]
            #print(left, upper, right, lower)
            d = image_cut2(image, left, upper, right, lower)
            if (d['code']==2):
                l_sit += [d['sit']]
                l_text += [d['ocr_text']]
                l_time += [d['cap_time']]
                if d['sit']!=-1:
                    l_sit2 += [d['sit']]
                    l_xtime += [d['x_time']]
                    jsq=jsq+1
            

    write_to_excel(l_sit,l_text,l_time)
    if jsq!=0:
        upload_to_server(l_sit2,l_xtime,jsq)#不需要服务器端可以把这个注释掉
    
    
    time.sleep(delay_time)
