<form class="form-horizontal" action="{:U('Crm/Member/config',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">类型名称:</td>
          <td>{$data.title}</td>
        </tr>
        <tr>
          <td width="130px">可办理区域:</td>
          <td><input type="text" name="area" size="35" /><span class="fun_tips">请输入允许办理的身份证号前4位，多个区域用”,“隔开</span></td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" name="id" value="{$data.id}">
    <?php dump(json_decode($data['rule'])); ?>
  </div>
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">保存</button>
      </li>
    </ul>
  </div>
</form>