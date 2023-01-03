# 腾讯会议网课摄像头监控程序 
> 本程序用于自动检测网课没开摄像头的同学

## 使用说明 
* 使用此程序需要一定的编程基础 
* 每位同学的入会昵称必须为：座号+“号”+其它内容，否则程序无法正常识别（当然你可以自己改程序） 
* 需要将腾讯会议窗口调整至1980*640像素，如窗口大小不对则无法识别 
* 程序运行过程中请保持腾讯会议窗口显示在最前方 
* 请将腾讯会议设置为25等分模式，并滚动鼠标至最后一页 
* 需要安装并正确配置Tesseract-OCR，并安装并配置pytesseract库（用于ocr读取入会昵称） 
* 本程序的运行间隔为7.5s，在我的电脑上程序每次运行需约2.5s，所以实际每次检测的时差是10s，由于每个人电脑速度不同，需要自行调整运行间隔时间 
* 本程序包含服务器端（使用php开发），用于实时对外展示数据和请假审批并自动扣除没开摄像头时长，请自行选用 
* 服务器端需要配合数据库，请自行调试 

## 程序原理
1. 对腾讯会议窗口整个进行截图 
2. 使用预先设好的坐标对每个同学的图像进行裁切 
3. 判断裁切后的图像的某个特殊像素点颜色（由于没开摄像头的同学只会显示一个头像，剩余部分的颜色为固定的特殊颜色，故由此来识别；此方法有一定误判的概率，但实际使用中基本没有大影响）
4. 对没开摄像头的同学截取左下角昵称的部分图片，并进行ocr识别，获取文本，并抽取出座号 
5. 保存截图证据和数据，并把数据上报云端服务器 
