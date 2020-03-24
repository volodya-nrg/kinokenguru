// ##########################
// ############# CONSTANTS
// ##########################

// время буфера для видео, в секундах
const BUFFER_TIME:Number = 5;
// звук по умолчанияю
const DEFAULT_VOLUME:Number = 0.8;
// обновление каждый 10 млсекунд
const DISPLAY_TIMER_UPDATE_DELAY:int = 50;
// сглаживание видео
const SMOOTHING:Boolean = true;
// отступ прогресс баров (вывели для упрощения)
const MARGIN_LEFT_PROGRESS:Number = 70;
// максимальная ширина прогресс баров (вывели для упрощения)
const PROGRESS_MAX_WIDTH:Number = 330;
// белый цвет
const COLOR_WHITE = "#ffffff";
// частота обновления прелоадера
const TIME_UPDATE_PRELOADER = 15;

// ##########################
// ############# VARIABLES
// ##########################

// флаг загрузки видео
var isLoaded:Boolean = false;
// переменная для хранения звука, на тот случай когда нажали mute
var intLastVolume:Number = DEFAULT_VOLUME;
// net connection object for net stream
var ncConnection:NetConnection;
// net stream object
var nsStream:NetStream;
// object holds all meta data
var objInfo:Object;
// адрес к файлу
//var strSource:String = "./the_dictator_2012.mp4";
var strSource:String = stage.loaderInfo.parameters.file_path;
// адрес к логотипу
//var logoPath:String = "../img/logo-v1.png";
var logoPath:String = stage.loaderInfo.parameters.logo_path;
// загрузчик картинки (логотипа)
var logoLoader:Loader = new Loader();
// timer for updating player (progress, volume...)
var tmrDisplay:Timer;
var tmrPreloader:Timer;
// флаг для того чтоб понять: воспроизводится ли в данный момент видео
var isNowPlaying:Boolean = false;
// длительность видео
var duration:Number = 0;

// ##########################
// ############# FUNCTIONS
// ##########################

// установка плеера
function initVideoPlayer():void {
	// выстроим в нужные места
		panel.x = 0;
		panel.y = stage.stageHeight;
		panel.width = stage.stageWidth;
		panel.height = 30;
	
		maskDisplay.x = 0;
		maskDisplay.y = 0;
		maskDisplay.width = stage.stageWidth;
		maskDisplay.height = stage.stageHeight - panel.height;
	
		//vidDisplay.x = 0;
		//vidDisplay.y = 0;
		//vidDisplay.width = stage.stageWidth;
		//vidDisplay.height = stage.stageHeight - panel.height;
	
		preloader.x = maskDisplay.width / 2;
		preloader.y = maskDisplay.height / 2;
		preloader.alpha = 0.5;
		preloader.visible = false;
	
		panel.progressFillBg.width = PROGRESS_MAX_WIDTH;
		panel.progressFillLoading.width = 0;
		panel.progressFillPlay.width = 0;
		panel.progressFillBg.x = panel.progressFillLoading.x = panel.progressFillPlay.x = MARGIN_LEFT_PROGRESS;
	
		alert.x = 10;
		alert.y = 10;
		alert.width = stage.stageWidth - 20;	
		alert.visible = false;
		
		begunok.x = 200;
		begunok.y = 100;
		begunok.text = "0:00:00";
		begunok.background = true;
		begunok.backgroundColor = 0xFF9900;
		begunok.visible = false;
		
	// спрячем пока не нужные кнопки
		panel.btnUnmute.visible	= false;
		panel.btnPause.visible	= false;
		panel.btnScaleOn.visible = true;
		panel.btnScaleOff.visible = false;
	
	// покажем время
		panel.lblTimeDuration.htmlText = "<font color='"+COLOR_WHITE+"'>"+formatTime(0)+"</font> / "+formatTime(0);
	
	// если есть логотип, то подгрузим его тоже
	if(logoPath !== null){
		logoLoader.load( new URLRequest(logoPath) );
		addChild(logoLoader);
		// здесь ширина картинки подстраивается под заданную высоту
		logoLoader.contentLoaderInfo.addEventListener(Event.COMPLETE, onLoadLogo);
	}
	
	// зададим z-index для основных элементов
		this.setChildIndex(vidDisplay, 0);
		this.setChildIndex(preloader, 1);
		this.setChildIndex(alert, 2);
		this.setChildIndex(maskDisplay, 3);
		this.setChildIndex(panel, 4);
		this.setChildIndex(begunok, 5);

	// добавим события на кнопки
		panel.btnPause.addEventListener(MouseEvent.CLICK, pauseClicked);
		panel.btnPlay.addEventListener(MouseEvent.CLICK, playClicked);
		panel.btnStop.addEventListener(MouseEvent.CLICK, stopClicked);
		panel.progressFillLoading.addEventListener(MouseEvent.CLICK, goToSeek);
		panel.progressFillPlay.addEventListener(MouseEvent.CLICK, goToSeek);
		panel.btnMute.addEventListener(MouseEvent.CLICK, muteClicked);
		panel.btnUnmute.addEventListener(MouseEvent.CLICK, unmuteClicked);
		panel.sndHide.addEventListener(MouseEvent.CLICK, volumeClicked);
		panel.btnScaleOn.addEventListener(MouseEvent.CLICK, onFullscreenOn);
		panel.btnScaleOff.addEventListener(MouseEvent.CLICK, onFullscreenOff);
		maskDisplay.addEventListener(MouseEvent.CLICK, togglePlayClicked);
		stage.addEventListener(Event.FULLSCREEN, onChangeFullscreen);
		stage.addEventListener(MouseEvent.MOUSE_MOVE, onStageMouseMove);
		
	// создадим таймер для обновления все видимых частей плеера и добавим event listener
		tmrDisplay = new Timer(DISPLAY_TIMER_UPDATE_DELAY);
		tmrDisplay.addEventListener(TimerEvent.TIMER, updateDisplay);
	
	// создадим таймер вращения прелоадера
		tmrPreloader = new Timer(TIME_UPDATE_PRELOADER);
		tmrPreloader.addEventListener(TimerEvent.TIMER, updatePreloader);

	// создадим новый net connection, добавим event listener и connect в null потому что мы не имеем media server
		ncConnection = new NetConnection();
		ncConnection.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
		ncConnection.connect(null);
	
	// создадим новый netstream с net connection, добавим event listener, установим клиента, а так же буффер
		nsStream = new NetStream(ncConnection);
		nsStream.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
		nsStream.client = this;
		nsStream.bufferTime = BUFFER_TIME; // +- 2 секунды

	// прикрепим net stream в видео объект на холсте
		vidDisplay.attachNetStream(nsStream);
	
	// установим сглаживание
		vidDisplay.smoothing = SMOOTHING;
	
	// установим звук
		var percentVol = DEFAULT_VOLUME * 100 / 1;
		panel.sndProgress.width = Math.round((panel.sndBg.width * percentVol)/100);
		setVolume(DEFAULT_VOLUME);
}
function onLoadLogo(e:Event):void {
	var margin = 10;

	logoLoader.height = 30;
	logoLoader.scaleX = logoLoader.scaleY;
	logoLoader.x = maskDisplay.width - logoLoader.width - margin;
	logoLoader.y = maskDisplay.height - logoLoader.height - margin;
	logoLoader.alpha = 0.5;
	
	// т.к. появился новый элемент, то переназначим z-index
	this.setChildIndex(vidDisplay, 0);
	this.setChildIndex(preloader, 1);
	this.setChildIndex(logoLoader, 2);
	this.setChildIndex(alert, 3);
	this.setChildIndex(maskDisplay, 4);
	this.setChildIndex(panel, 5);
	this.setChildIndex(begunok, 6);
}
function playClicked(e:MouseEvent):void {
	// если файл не загружен, подгрузим
	if(!isLoaded) {
		nsStream.play(strSource);
		isLoaded = true;
	
	} else {
		nsStream.resume();
	}
	
	// покажем видео объект
	vidDisplay.visible = true;
	
	// запустим счетчик прелоадера
	if(tmrPreloader.running === false){
		tmrPreloader.start();
	}

	// переключим нужные кнопки
	panel.btnPause.visible = true;
	panel.btnPlay.visible = false;
	isNowPlaying = true;
	logoLoader.visible = false;
}
function pauseClicked(e:MouseEvent):void {
	// остановим видео
	nsStream.pause();
	
	// переключим нужные кнопки
	panel.btnPause.visible = false;
	panel.btnPlay.visible = true;

	isNowPlaying = false;
}
function stopClicked(e:MouseEvent):void {
	// вызовим ф-ию стоп
	stopVideoPlayer();
}
function togglePlayClicked(e:MouseEvent):void{
	if(isNowPlaying === true){
		pauseClicked(e);
		
	} else {
		playClicked(e);
	}
}
function muteClicked(e:MouseEvent):void {
	// сохраним значение
	intLastVolume = nsStream.soundTransform.volume;
	
	// установим по нулям
	setVolume(0);
	panel.sndProgress.width = 0;
}
function unmuteClicked(e:MouseEvent):void {
	//  восстановим значение
	setVolume(intLastVolume);
	
	// установи ширину
	var percentVol = intLastVolume * 100 / 1;
	panel.sndProgress.width = Math.round(percentVol * panel.sndBg.width / 100);
}
function volumeClicked(e:MouseEvent):void {
	// установим ширину	
	panel.sndProgress.width = e.stageX - panel.sndHide.x;
	
	// расчитаем звук
	var percentWidth = Math.round(panel.sndProgress.width * 100 / panel.sndBg.width);	
	var vol = 1 * percentWidth / 100;
	
	setVolume(vol);
}
function goToSeek(e:MouseEvent):void {
	var sec = getSecViaPx(e.stageX - MARGIN_LEFT_PROGRESS);
	nsStream.seek(sec);
	
	if(isNowPlaying === false){
		playClicked(e);
	}
}
function updateDisplay(e:TimerEvent):void {
	if(nsStream.bytesLoaded === nsStream.bytesTotal){
		panel.progressFillLoading.width = PROGRESS_MAX_WIDTH;
	
	} else {
		var percentLoaded = (nsStream.bytesLoaded * 100) / nsStream.bytesTotal;
		panel.progressFillLoading.width = Math.round(PROGRESS_MAX_WIDTH * percentLoaded / 100);
	}
	
	if(nsStream.time === duration){
		panel.progressFillPlay.width = PROGRESS_MAX_WIDTH;
		
	} else {
		var percentTime = (nsStream.time * 100) / duration;
		panel.progressFillPlay.width = Math.round(PROGRESS_MAX_WIDTH * percentTime / 100);
	}
	
	panel.lblTimeDuration.htmlText = "<font color='"+COLOR_WHITE+"'>"+formatTime(nsStream.time)+
									 "</font> / "+formatTime(duration);						 
}
function updatePreloader(e:TimerEvent):void {
	if(nsStream.bufferLength === 0){
		if(preloader.visible === false){
			preloader.visible = true;
		}

		preloader.rotation += 10;		
		e.updateAfterEvent();

	} else if(preloader.visible === true) {
		preloader.visible = false;
	}
}
function onMetaData(info:Object):void {
	// установим метаданные в объект
	objInfo = info;
	duration = Math.round(objInfo.duration);
	
	// т.к. данные уже известны, выравним объект
	setPositionVideo();

	// теперь мы может запустить таймер, потому что мы имеет все данные
    tmrDisplay.start();
}
function netStatusHandler(event:NetStatusEvent):void {
	// происходящие события
	switch (event.info.code) {
		case "NetStream.Buffer.Full":
			break;
		case "NetStream.Buffer.Empty":
			break;
		case "NetStream.Play.Start":
			break;
		case "NetStream.Seek.Notify":
			break;
		case "NetStream.Seek.InvalidTime":
			break;
		case "NetStream.Play.StreamNotFound":
			alert.text = "Во время загрузки произошла ошибка. Повторите попытку позже.";
			alert.visible = true;
			stopVideoPlayer();
			
			break;
		case "NetStream.Play.Stop":
			stopVideoPlayer();
			break;	
	}
}
function stopVideoPlayer():void {
	// остановим netstream, и установим время на ноль
	nsStream.pause();
	nsStream.seek(0);

	// для того, чтобы очистить дисплей, необходимо установить видимость ложной, так как функция имеет ошибку
	vidDisplay.visible = false;
	
	// переключим кнопки и флаг
	panel.btnPause.visible = false;
	panel.btnPlay.visible = true;
	
	isNowPlaying = false;
	logoLoader.visible = true;

	// остановим счетчик прелоадера
	tmrPreloader.stop();
	preloader.visible = false; // на всякий случай сроекм его
}
function setVolume(intVolume:Number = 0):void {
	// создадим soundtransform object с параметром громкости
	var sndTransform = new SoundTransform(intVolume);
	nsStream.soundTransform	= sndTransform;
	
	// переключим необходимые кнопки
	if(intVolume > 0) {
		panel.btnMute.visible	= true;
		panel.btnUnmute.visible	= false;
		
	} else {
		panel.btnMute.visible	= false;
		panel.btnUnmute.visible	= true;
	}
}
function formatTime(t:int):String {
	if(t <= 0){
		return "0:00:00";
	}
	
	var h:int = Math.floor(t / 3600);
	var m:int = Math.floor((t - h * 3600) / 60);
	var s:int = Math.floor(t - (h * 3600) - (m * 60));

	return String(h + ":" + (m < 10 ? "0" : "") + m + ":" + (s < 10 ? "0" : "") + s);
}
function formatTimeV2(t:int):String {
	var output = "";
	var h:int = Math.floor(t / 3600);
	var m:int = Math.floor((t - h * 3600) / 60);
	var s:int = Math.floor(t - (h * 3600) - (m * 60));

	if(h === 0){
		output = String(m + ":" + (s < 10 ? "0" : "") + s);
		
	} else {
		output = String(h + ":" + (m < 10 ? "0" : "") + m + ":" + (s < 10 ? "0" : "") + s);
	}

	return output;
}
function onFullscreenOn(event:MouseEvent):void {
	stage.displayState = StageDisplayState.FULL_SCREEN;
}
function onFullscreenOff(event:MouseEvent):void {
	stage.displayState = StageDisplayState.NORMAL;
}
function onChangeFullscreen(event:FullScreenEvent):void {
	if(event.fullScreen){
		panel.btnScaleOn.visible = false;
		panel.btnScaleOff.visible = true;
		panel.y = stage.stageHeight + panel.height * 2;
		maskDisplay.height = stage.stageHeight;

	} else {
		panel.btnScaleOn.visible = true;
		panel.btnScaleOff.visible = false;
		panel.y = stage.stageHeight;
		maskDisplay.height = stage.stageHeight - panel.height;
	}
	
	setPositionVideo();
}
function onStageMouseMove(event:MouseEvent):void {
	if(stage.displayState === StageDisplayState.FULL_SCREEN){
		if(event.stageY >= maskDisplay.height - panel.height){
			panel.y = stage.stageHeight;

		} else {
			panel.y = stage.stageHeight + panel.height * 2;
		}
	}
	
	if(duration > 0){
		var x_1 = MARGIN_LEFT_PROGRESS;
		var x_2 = x_1 + panel.progressFillLoading.width;// x_1 + PROGRESS_MAX_WIDTH;
		var y_1 = 370;
		var y_2 = y_1 + 10;
		
		if(
			event.stageX > x_1 && event.stageX < x_2
				&&
			event.stageY > y_1 && event.stageY < y_2	
		){
			var sec = getSecViaPx(event.stageX - x_1);
			
			begunok.width = sec < 3600? 40: 50;
			begunok.x = event.stageX - begunok.width/2;
			begunok.y = panel.y - panel.height - 10;
			begunok.text = formatTimeV2(sec);
			begunok.visible = true;
			
		} else if(begunok.visible === true) {
			begunok.visible = false;	
		}
	}
}
function getSecViaPx(curPx):int {
	var percent = curPx * 100 / PROGRESS_MAX_WIDTH;
	var sec = Math.round(duration * percent / 100);
	
	return sec;
}
function setPositionVideo():void {
	var c = 0;
	
	// если видео меньше размера видеоОбъекта
	if(objInfo.width < maskDisplay.width && objInfo.height < maskDisplay.height){
		vidDisplay.width = objInfo.width;
		vidDisplay.height = objInfo.height;
		vidDisplay.x = (maskDisplay.width - vidDisplay.width)/2;
		vidDisplay.y = (maskDisplay.height - vidDisplay.height)/2;
	
	// если ширина видео меньше ширины дисплея, смаштабируем относительно высоты	
	} else if(objInfo.width < maskDisplay.width) {
		c = (maskDisplay.height * 100 / objInfo.height) / 100;
		
		vidDisplay.width = Math.round(objInfo.width * c);
		vidDisplay.height = maskDisplay.height;
		vidDisplay.x = (maskDisplay.width - vidDisplay.width) / 2;
		vidDisplay.y = 0;
	
	// иначе смаштабируем относительно ширины
	} else {
		c = (maskDisplay.width * 100 / objInfo.width) / 100;

		vidDisplay.width = maskDisplay.width;
		vidDisplay.height = Math.round(objInfo.height * c);
		vidDisplay.x = 0;
		vidDisplay.y = (maskDisplay.height - vidDisplay.height) / 2;
		
		//trace(objInfo.width, objInfo.height);
	}	
}

// ##########################
// ############# INIT PLAYER
// ##########################
initVideoPlayer();