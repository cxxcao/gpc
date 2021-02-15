
var sorbtek = new TimelineLite({onComplete:restartAnimation});

function startAnimation() {
    
    //screen 1
    sorbtek.to("#anim-bluesurface", 1, {opacity:"1"});
    sorbtek.to("#anim-yellowsurface", 1, {opacity:"1"}, "-=0.5");
    sorbtek.to("#anim-1", 1, {opacity:"1"}, "-=0.5");
    sorbtek.to("#anim-catchesmoisture", 1.5, {opacity:"1"}, "-=1");
    sorbtek.fromTo("#anim-catchesmoisture", 1.5, {top:"30px", left:"5px"}, {top:"30px", left:"15px"}, "-=1.5");
    
    sorbtek.to("#anim-moisture-bottom-1", 1, {opacity:"1"}, "-=1.5");
    sorbtek.to("#anim-moisture-bottom-3", 1, {opacity:"1"}, "-=1");
    
    //screen 2
    sorbtek.to("#anim-fromskin", 1.5, {opacity:"1"}, "-=1");
    sorbtek.fromTo("#anim-fromskin", 1.5, {top:"60px", left:"100px"}, {top:"60px", left:"120px"}, "-=1.5");    
    sorbtek.to("#anim-moisture-bottom-2", 1, {opacity:"1"}, "-=1");

    sorbtek.to("#anim-catchesmoisture", 0.5, {opacity:"0"}, "+=1");
    sorbtek.to("#anim-fromskin", 0.5, {opacity:"0"}, "-=0.2");
    sorbtek.to("#anim-1", 0.5, {opacity:"0"}, "-=0.2"); 

    //screen 3
    sorbtek.to("#anim-2", 1, {opacity:"1"}, "+=0.2");
    sorbtek.to("#anim-moves", 1.5, {opacity:"1"}, "-=1");
    sorbtek.fromTo("#anim-moves", 1.5, {top:"35px", left:"50px"}, {top:"35px", left:"70px"}, "-=1.5");
    sorbtek.to("#anim-moisture-bottom-1", 1, {opacity:"0", top:"-10px"}, "-=1.5");
    sorbtek.to("#anim-moisture-middle-1", 1, {opacity:"1"}, "-=1.5");
    sorbtek.to("#anim-moisture-bottom-2", 1, {opacity:"0", top:"-10px"}, "-=1");
    sorbtek.to("#anim-moisture-middle-2", 1, {opacity:"1"}, "-=1");
    
    //screen 4
    sorbtek.to("#anim-itout", 1.5, {opacity:"1"}, "-=1");
    sorbtek.fromTo("#anim-itout", 1.5, {top:"60px", left:"100px"}, {top:"60px", left:"120px"}, "-=1.5");   
    sorbtek.to("#anim-moisture-bottom-3", 1, {opacity:"0", top:"-10px"}, "-=1"); 
    sorbtek.to("#anim-moisture-top-1", 1, {opacity:"1"}, "-=1");
    sorbtek.to("#anim-yellowsurface", 0.5, {opacity:"0.5"});  
    
    sorbtek.to("#anim-moves", 0.5, {opacity:"0"}, "+=0.5");
    sorbtek.to("#anim-itout", 0.5, {opacity:"0"}, "-=0.2");
    sorbtek.to("#anim-2", 0.5, {opacity:"0"}, "-=0.2");
    
    //screen 5
    sorbtek.to("#anim-3", 1, {opacity:"1"}, "+=0.2");
    sorbtek.to("#anim-releasesit", 1.5, {opacity:"1"}, "-=1");
    sorbtek.fromTo("#anim-releasesit", 1.5, {top:"130px", left:"10px"}, {top:"130px", left:"30px"}, "-=1.5");
    sorbtek.to("#anim-moisture-middle-2", 1, {opacity:"0.7"}, "-=1.5");
    sorbtek.fromTo("#anim-moisture-top-2", 1, {opacity:"0", top:"5px"}, {opacity:"1", top:"0px"}, "-=1.5");
    sorbtek.to("#anim-moisture-middle-1", 1, {opacity:"0.7"}, "-=1");
    sorbtek.fromTo("#anim-moisture-top-3", 1, {opacity:"0", top:"0px"}, {opacity:"1", top:"-5px"}, "-=1");

    
    //screen 6
    sorbtek.to("#anim-tokeepyoudrier", 1.5, {opacity:"1"}, "-=1");
    sorbtek.fromTo("#anim-tokeepyoudrier", 1.5, {top:"160px", left:"65px"}, {top:"160px", left:"85px"}, "-=1.5");   
    sorbtek.to(["#anim-moisture-middle-1", "#anim-moisture-middle-2"], 1, {opacity:"0.4"}, "-=1");
    sorbtek.fromTo("#anim-moisture-top-4", 1, {opacity:"0", top:"10px"}, {opacity:"1", top:"0"}, "-=1");
    
    sorbtek.to("#anim-releasesit", 0.5, {opacity:"0"}, "+=1");
    sorbtek.to("#anim-tokeepyoudrier", 0.5, {opacity:"0"}, "-=0.2");
    sorbtek.to("#anim-3", 0.5, {opacity:"0"}, "-=0.2");
    
    //screen 7
    sorbtek.to("#anim-logo", 0.5, {opacity:"1"}, "+=1");
    sorbtek.to("#anim-subtitle", 0.5, {opacity:"1"});
    
    sorbtek.to(["#anim-bluesurface", "#anim-yellowsurface", "#anim-moisture-middle-1", "#anim-moisture-middle-2", "#anim-moisture-top-1", , "#anim-moisture-top-2", , "#anim-moisture-top-3", "#anim-moisture-top-4"], 1, {opacity:"0"}, "+=2");
    sorbtek.to(["#anim-logo", "#anim-subtitle"], 1, {opacity:"0"});
    sorbtek.to(["#anim-moisture-bottom-1", "#anim-moisture-bottom-2", "#anim-moisture-bottom-3"], 1, {top:"0"});
}

function restartAnimation() {
    sorbtek.restart();
}
