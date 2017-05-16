var RingCollection = function(el){
    var currentRing = null;
	this.init = function(el){
		this.element = el;

		this.ringElements = this.element.querySelectorAll(".ring");
		this.rings = [];
		this.leftClicker = this.element.querySelector(".left-arrow");
		this.rightClicker = this.element.querySelector(".right-arrow");
        this.dataBox = this.element.querySelector(".ring-data");

		this.leftClicker.addEventListener("click", this.moveRight.bind(this));
		this.rightClicker.addEventListener("click", this.moveLeft.bind(this));
		this.lastAnimateTime = Date.now();
        this.doubleTime = false;

		//translate elements object to rings array
		for(var i = 0; i < this.ringElements.length;++i){
			this.rings.push(this.ringElements[i]);
            this.ringElements[i].addEventListener("click", this.gotoRing.bind(this));
		}
        
        var dupCounter = 0;
        while(this.rings.length < 7){
            var tempRing = this.rings[dupCounter].cloneNode(true);
            tempRing.addEventListener("click", this.gotoRing.bind(this));
            this.rings.push(tempRing);
            this.dataBox.parentNode.insertBefore(tempRing, this.dataBox);
            dupCounter++;
        }
        
		this.adjustRings();

        // recenter collection on browser resize.
        this.addWindowResizeEvent();
        this.recenterCollection();
	};
        
    this.addWindowResizeEvent = function(){
        var ringCollection = this;
        if(window.attachEvent) {
            window.attachEvent('onresize', function() {
                ringCollection.recenterCollection();
            });
        }
        else if(window.addEventListener) {
            window.addEventListener('resize', function() {
                ringCollection.recenterCollection();
            }, true);
        }
    };

    this.recenterCollection = function(){
        var ringHolder = document.getElementById("ring-holder");
        var width = ringHolder.clientWidth;
        var wSize = this.getWindowSize();
        if(wSize.width >= width){
            ringHolder.style.left = "0";
            return;
        }
        var left = -((width - wSize.width)/2);
        ringHolder.style.left = left + "px";
    };

    this.getWindowSize = function(){
        var w=window,
        d=document,
        e=d.documentElement,
        g=d.getElementsByTagName('body')[0],
        width =w.innerWidth||e.clientWidth||g.clientWidth,
        height =w.innerHeight||e.clientHeight||g.clientHeight;
        return {
            width:width,
            height:height
        }
    };

    this.gotoRing = function(ev){
        if(this.isAnimating()){
			return;
		}
        var chosenRing = null;
        if(ev instanceof Event){
            chosenRing = ev.target;
        }else{
            chosenRing = ev;
        }
        
        if(chosenRing.classList.contains("ring3")){
            window.location.href = chosenRing.dataset.link;
            return;
        } else if(chosenRing.classList.contains("ring1")){
            this.addDoubleTime();
            this.moveRight();
            setTimeout(this.moveRight.bind(this), 250);
            setTimeout(this.removeDoubleTime.bind(this), 501);
        } else if(chosenRing.classList.contains("ring2")){
            this.moveRight();
        } else if(chosenRing.classList.contains("ring4")){
            this.moveLeft();
        } else if(chosenRing.classList.contains("ring5")){
            this.addDoubleTime();
            this.moveLeft();
            setTimeout(this.moveLeft.bind(this), 250);
            setTimeout(this.removeDoubleTime.bind(this), 501);
        }
    };

	this.moveLeft = function(){
		if(this.isAnimating()){
			return;
		}
		this.rings.push(this.rings.shift());
		this.adjustRings();
	};

	this.moveRight = function(){
		if(this.isAnimating()){
			return;
		}
		this.rings.unshift(this.rings.pop());
		this.adjustRings();
	};

	this.adjustRings = function(){
		this.lastAnimateTime = Date.now();
		for(var i = 0; i < this.rings.length;++i){
			this.clearRingLocations(this.rings[i]);
			this.rings[i].classList.add("ring" + i);
		}
		var activeRing = this.rings[3]; //yep, always position 3.
        currentRing = activeRing;
		var nameEl = this.element.querySelector(".ring-name");
		var linkEl = this.element.querySelector(".ring-link");

        var str = "";
        console.log(activeRing);
        if(typeof activeRing.dataset.award != "undefined"){
            str = '<img src="' + activeRing.dataset.award + '" />';
        }else{
            str = activeRing.alt;
        }

		nameEl.innerHTML = str;
		linkEl.href = activeRing.dataset.link;
        nameEl.href = activeRing.dataset.link;
	};
    
    this.clearRingLocations = function(ring){
        for(var i = 0; i < this.rings.length;++i){
            ring.classList.remove("ring" + i);
        }
    };

	this.isAnimating = function(){
		var now = Date.now();
        var waitTime = 498;
        if(this.doubleTime){
            waitTime /= 2;
        }
		if(now - this.lastAnimateTime > waitTime){
			return false;
		}
		return true;
	};
    
    this.addDoubleTime = function(){
        this.doubleTime = true;
        for(var i = 0; i < this.rings.length;++i){
            this.rings[i].classList.add("double-time");
        }
    };
    
    this.removeDoubleTime = function(){
        this.doubleTime = false;
        for(var i = 0; i < this.rings.length;++i){
            this.rings[i].classList.remove("double-time");
        }
    };

	this.init(el);
};