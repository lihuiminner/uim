/**

 @Name：layuiAdmin 公共业务
 @Author：贤心
 @Site：http://www.layui.com/admin/
 @License：LPPL
    
 */

layui.define(function (exports) {
  var $ = layui.$
    , layer = layui.layer
    , laytpl = layui.laytpl
    , setter = layui.setter
    , view = layui.view
    , admin = layui.admin

  //公共业务的逻辑处理可以写在此处，切换任何页面都会执行
  //……


  console.log('公共业务')
  //退出
  // admin.events.logout = function(){
  //   //执行退出接口
  //   admin.req({
  //     url: layui.setter.base + 'json/user/logout.js'
  //     ,type: 'get'
  //     ,data: {}
  //     ,done: function(res){ //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行

  //       //清空本地记录的 token，并跳转到登入页
  //       admin.exit(function(){
  //         location.href = 'user/login.html';
  //       });
  //     }
  //   });
  // };
  $('[uim-power]').each(function () {
    uim.power_check($(this).attr('uim-power')) && $(this).show();
  })


  var listEnumSelect = function (tag_name, enum_obj) {
    var i = 1, arr = [];
    while (true) {
      if (enum_obj[i]) {
        switch (tag_name) {
          case 'SELECT':
            arr.push('<option value="' + i + '">' + enum_obj[i] + '</option>');
            break;
          case 'DATALIST':
            arr.push('<option value="' + enum_obj[i] + '" />');
            break;
        }
        i++;
      } else {
        break;
      }
    }
    return arr.join('');
  },
    init_enum_select = function () {
      $('[uim-enum]').each(function () {
        $(this).append(listEnumSelect(this.tagName, eval('uim.enum.' + $(this).attr('uim-enum'))));
      });
    },
    // 将select元素自动加载接口数据；todo: table也可以这样做，另指定一行模板，但需要考虑接口传入参数合理性
    init_api_select = function () {
      // var get_api_list = [], tmp_api_list = [];
      $('select[uim-api]').each(function () {
        var select_obj = this;
        var api_name = $(select_obj).attr('uim-api'), api_data = {}, list_name = $(select_obj).attr('uim-api-list').split(',');
        var tmp_api_data = $(select_obj).attr('uim-api-data') ? $(select_obj).attr('uim-api-data').split(',') : [];
        $(tmp_api_data).each(function () {
          var tmp_data = this.split(':');
          api_data[tmp_data[0]] = eval(tmp_data[1]);
        });
        // tmp_api_list.indexOf(api_name) == -1 && tmp_api_list.push(api_name) && 
        uim.api.emit({
          code: api_name,
          data: api_data,
          success: function (data) {
            var data_list = [];
            $(data[list_name[0]]).each(function () {
              data_list.push('<option value="' + this[list_name[1]] + '">' + this[list_name[2]] + '</option>')
            })
            // $('select[uim-api="' + api_name + '"]').append(data_list.join(''));
            $(select_obj).append(data_list.join(''));
            layui.form.render();
          },
          error: function (err) {
            $(select_obj).append('<option value="">【数据接口请求失败】</option>');
            layui.form.render();
          }
        })
      });
      // get_api_list.length && uim.api.emit(get_api_list);
    },
    // 将select元素自动加载数据
    init_data_select = function (select_objs, data_list, data_i, data_v, data_ext = []) {
      $(data_list).each(function () {
        var tmp_option = $('<option value="' + this[data_i] + '"' + (data_ext.length ? (' data-' + data_ext.join('="" data-') + '=""') : '') + '>' + this[data_v] + '</option>')
        var that = this;
        $(data_ext).each(function () {
          tmp_option.data(this, that[this]);
        });
        $(select_objs).append(tmp_option);
      })
      layui.form.render();
    }
  // $(function () {
  //   init_enum_select();
  //   init_api_select();
  // })


  //对外暴露的接口
  exports('common', {
    init_enum_select,
    init_api_select,
    init_data_select,
  });
});


function uim_open(page_args, layer_args) {

  if (typeof page_args == 'object') {
    window.page_args = page_args;
  }
  var c = {
    offset: 'auto',
    closeBtn: 1,
    maxmin: true,
    shade: 0.2,
    shadeClose: false,
    type: 2,
    area: ['50%', '50%'],
    fix: false, //不固定
    // title: '',
    // content: '',
    success: function (layero, index) {
      $(document.activeElement).blur();
    },
  };
  $.extend(c, layer_args);

  if (c.type == 2 && c.content.indexOf('?') == -1) {
    c.content += '?' + Math.random();
  }
  return layer.open(c);
}


var layer_right = function (index, url, width = '80%') {
  // 使用window.name将index变量传入右侧呼出页面
  top.layui.admin.popupRight({
      id: 'LAY_adminPopupLayerTest',
      area: [width],
      success: function () {
          top.$('#' + this.id).css('height', '100%').html('<iframe name="' + index + '" style="border:none;width:100%;height:100%;" src="' + url + '"></iframe>');
      }
  });
}