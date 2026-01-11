(function($){
  $(function(){
    var map = {
      'Sunday':'Domingo','Monday':'Segunda-feira','Tuesday':'Terça-feira','Wednesday':'Quarta-feira','Thursday':'Quinta-feira','Friday':'Sexta-feira','Saturday':'Sábado',
      'domingo':'Domingo','segunda-feira':'Segunda-feira','terça-feira':'Terça-feira','quarta-feira':'Quarta-feira','quinta-feira':'Quinta-feira','sexta-feira':'Sexta-feira','sábado':'Sábado'
    };
    $('.forecast-day .day-name').each(function(){
      var t = $(this).text().trim();
      if (map[t]) $(this).text(map[t]);
    });
  });
})(jQuery);
