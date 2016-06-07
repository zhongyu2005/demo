/**
 * Created by zhongyu on 2016/5/19.
 */
$(function(){
    def.baobao={sex:1};
    var imgs = ["baobao/img/sex_nan_on.png","baobao/img/sex_nv_on.png","baobao/img/sex_nan.png","baobao/img/sex_nv.png"];
    $('.txzl_sex .click_sex').click(function(){
        var index = $(this).index();
        if(index == 1){
            def.baobao.sex=1;
            $('.txzl_sex_nan img').attr('src',imgs[0]);
            $('.txzl_sex_nv img').attr('src',imgs[3]);
        }
        else if(index == 2){
            def.baobao.sex=2;
            $('.txzl_sex_nv img').attr('src',imgs[1]);
            $('.txzl_sex_nan img').attr('src',imgs[2]);
        }
    });
    var colors = ['#41d3c3','#fa8da0','#fd8b5a','#74bbff','#eeaa17','#ba8ad0','#cadd7e','#d98bd6','#61dd84','#dd6161','#617edd','#77da41'];
    var lilength = $('.txzl_tedian li').length;
    for( var i = 0; i < lilength ; i++){
        $('.txzl_tedian li').eq(i).css({ 'color':colors[i],'border-color':colors[i]});
    }
    $('.txzl_tedian li').each(function(i){
        $(this).css({ 'color':colors[i],'border-color':colors[i]}).data('isClick',false);
    });
    $('.txzl_tedian li').click(function(){
        var index = $(this).index();

        if($(this).data('isClick')){
            $(this).css({ 'background-color':'#fff','color':colors[index]});
        }else{
            var num=0;
            $(this).siblings().each(function () {
                if($(this).data("isClick")){
                    num++;
                }
            });
            if(num>=4){
                return false;
            }
            $(this).css({ 'background-color':colors[index],'color':'#fff'});
        }
        $(this).data('isClick',!$(this).data('isClick'));
    });
    $('#txzl2_text').on('input',function(){
        var inputlength = $(this).val().length;
        if(inputlength >100){
            var sub = $(this).val().substr(0,100);
            $(this).val(sub);
            inputlength=100;
        }
        $('.txzl2_zishu i').html(inputlength) ;
    });

    //当图片上传成功 叉显示
    //$('.txzl2_cha').addClass('show');
    $('.txzl2_cha').click(function(){
        $(this).siblings('.txzl2_pic').children('img').attr('src','baobao/img/jiahao.png');
        $(this).hide();
    });

    $(".zdle_btn").on("click",function(){
        $(this).closest(".txzl_opo").addClass("hide");
    })
});

$(function () {
    getCityArea();
    $('#show').click(function(){
        $('#search-ui').mobiscroll('show');
        return false;
    });
    $('#clear').click(function () {
        $('#search-ui').mobiscroll('clear');
        return false;
    });
    $("#next1").on("click",function () {
        var name=$("#name");
        var year=$("#year").val();
        var month=$("#month").val();
        var hobby=[];
        $('.txzl_tedian li').each(function () {
            if($(this).data("isClick")){
                hobby.push($(this).text());
            }
        });
        if(name.val()==''){
            name.focus().select();
            return false;
        }
        var max=year*12 + month*1;
        if(max>168 || max<=0){
            showTips("萌娃参赛年龄最小为1个月，最大为14周岁");
            return false;
        }
        if(hobby.length<=0 || hobby.length>4){
            showTips("宝宝特点最少选择1项，最多4项");
            return false;
        }
        $("#next-div1").addClass("hide");
        $("#next-div2").removeClass("hide");
        def.baobao.name=name.val();
        def.baobao.age=max;
        def.baobao.hobby=hobby.join(",");
        init_pic();
        document.title=' 萌娃资料填写（2/2） ';
    });
    $("#prev1").on("click",function () {
        $("#next-div2").addClass("hide");
        $("#next-div1").removeClass("hide");
        document.title=' 萌娃资料填写（1/2） ';
    });
    $("#prev2").on("click",function () {
        $("#next-div3").addClass("hide");
        $("#next-div2").removeClass("hide");
        document.title=' 萌娃资料填写（2/2） ';
    });
    $("#next2").on("click",function () {
        var pic=$(".txzl2_cha:visible").length;
        if(pic<=0){
            showTips("请至少上传一张图片");
            return false;
        }
        var txt=$("#txzl2_text");
        if(txt.val()==''){
            txt.focus().select();
            return false;
        }
        var img=[];
        $(".txzl2_cha:visible").parent().find(".txzl2_pic img").each(function(){
            img.push($(this).attr("src"));
        });
        def.baobao.img=img.join(',');
        def.baobao.intro=txt.val();
        $("#next-div2").addClass("hide");
        $("#next-div3").removeClass("hide");
        document.title=' 家长资料填写 ';
    });
    $("#next3").on("click",function () {
        //提交
        var pname=$("#p_name");
        var phone=$("#p_phone");
        var vcode=$("#vcode");
        var city=$("#search-ui_dummy");
        var ar=[pname,phone,vcode,city];
        var flg=false;
        $.each(ar,function (k,v) {
            if(v.val()==''){
                v.focus().select().click();
                flg=true;
                return false;
            }
        });
        if(flg){
            return false;
        }
        if(!test_reg(phone.val(),'tel')){
            phone.focus().select();
            return false;
        }
        if(vcode.val().length!=6){
            vcode.focus().select();
            return false;
        }
        def.baobao.parent_name=pname.val();
        def.baobao.parent_phone=phone.val();
        def.baobao.vcode=vcode.val();
        def.baobao.city=city.val();
        var url=location.href.toString();
        if(def.lock=='1')return false;
        def.lock=1;
        ajax_post(url,def.baobao,function (j) {
            def.lock=0;
            if(j.error=='1'){
                def.lock=0;
                showTips(j.msg);
                return false;
            }
            if(j.error=='200'){
                location=j.data.url;
            }
        })
    });

    $("#scode").on("click",function () {
        var url=location.origin+location.pathname+"?a=BaoBao&m=sendSMS";
        var phone=$("#p_phone");
        if(!test_reg(phone.val(),'tel')){
            phone.focus().select();
            return false;
        }
        var data={
            submit:'submit',phone:phone.val(),
        };
        if(def.vlock=='1'){
            return false;
        }
        def.vlock=1;
        ajax_post(url,data,function (j) {
            if(j.error=='1'){
                def.vlock=0;
                showTips(j.msg);
                return false;
            }
            if(j.error=='200'){
                showTips("验证码发送成功");
                //60秒倒计时
                $("#scode").find("img").hide().end().append("<span>59秒后重发</span>");
                var sp=$("#scode span");
                (function fade(){
                    var num=parseInt(sp.text())-1;
                        sp.text(num+"秒后重发");
                        num<=1?[sp.prev("img").show(),sp.remove(),def.vlock=0]:setTimeout(fade,1000)
                })();
            }
        });
    })

});

function showTips($msg){
    var tips=$(".txzl_opo").removeClass("hide");
    tips.find("p").html($msg);
}
function init_pic() {
    var div=$("#next-div2").find(".txzl2_pic");
    var url=location.origin+location.pathname+"?a=BaoBao&m=upload";
    var attr={
        browse_button : 'btnUpload',
        url : url,
        flash_swf_url : '../Public/js/plupload/Moxie.swf',
        silverlight_xap_url : '../Public/js/plupload/Moxie.xap',
        filters:{
            mime_types : [
                { title: "Image files", extensions: "jpg,gif,png,jpeg,bmp" }
            ],
        },
        resize: {
            width: 1440,
            height: 960,
            crop: false,
            quality: 80,
            preserve_headers: false
        },
        multi_selection:false,
    };
    div.each(function(k){
        var id='up'+k;
        $(this).attr('id',id);
        attr.browse_button=id;
        var uploader1 = new plupload.Uploader(attr);
        uploader1.init(); //初始化
        uploader1.bind('FilesAdded',function(uploader,files){
            uploader.start();
        });
        uploader1.bind("FileUploaded",function(uploader,file,responseObject){
            upload_back(responseObject.response,$("#"+id));
        });
        uploader1.bind("UploadProgress",function(uploader,file){
            //进度条
            var html=file.percent+"%";
            var obj=$("#"+id);
            var sp=obj.find("span");
            obj.find("img").addClass("hide");
            if(sp.length>0){
                sp.html(html);
            }else{
                obj.append("<span>"+html+"</span>");
            }
        });
        uploader1.bind("Error",function(uploader,responseObject){
            showTips(responseObject.message);
        });
    });
}
function upload_back(r,obj){
    var j=JSON.parse(r);
    if(j && j.data){
        obj.find("img").removeClass("hide").attr("src",j.data);
        obj.find("span").remove();
        obj.nextAll(".txzl2_cha").show();
        $("#picNum").html($(".txzl2_cha:visible").length);
    }else{
        showTips(r.msg);
    }
}
function getCityArea(){
    var url='http://pic.wx.suofeiya.com.cn/static/cj/city/city.json';
    $.getScript(url,function(){
        var ui=$("#search-ui");
        var s='';
        $.each(cityJson,function(i,v){
            s+='<li data-val="'+v.name+'">'+v.name;
            if(v.city){
                var cs='<ul>';
                $.each(v.city,function(ci,cv){
                    cs+=' <li data-val="'+cv.name+'">'+cv.name;
                    if(cv.area){
                        var cas='<ul>';
                        $.each(cv.area,function(cai,cav){
                            cas+='<li data-val="'+cav+'">'+cav+'</li>';
                        })
                        cas+="</ul>";
                        cs+=cas;
                        cas='';
                    }
                    cs+='</li>';
                })
                cs+="</ul>";
                s+=cs;
                cs='';
            }
            s+="</li>";
        });
        ui.html(s);
        s='';
        $('#search-ui').mobiscroll().treelist({
            theme: 'mobiscroll',
            lang: 'zh',
            display: 'bottom',
            fixedWidth: [160,140,140],
            placeholder: '请选择城市 ...',
// 	        defaultValue:['广东省', '广州市', '白云区'],
            labels: ['省份', '市区', '区域']
        });
        $("#search-ui_dummy").prop("readonly","readonley").css("ime-mode","disabled");
        $('.jz_add_in input').css('border',0);
        $('.jz_add_in input').attr('placeholder','省/市/区');
        $('.jz_add_in input').css('background-color','transparent');
    });
}
function log(){
    console.log(arguments);
}
