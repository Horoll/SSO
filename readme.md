# php单点登录实例
  单点登录英文全称Single Sign On，简称就是SSO。它的解释是：在多个应用系统中，只需要登录一次，就可以访问其他相互信任的应用系统
  
  例如我们登录使用淘宝网站后，再打开天猫，即使是两个不同的域名，也可以做到免登录。
  
  在传统的登录认证中，利用的是cookie和session记录客户端的状态和用户信息。但是在多系统中，因为cookie无法跨域，并且每个应用服务器上的session也不共享，无法达到单点登录的需求。
  
  通过一个统一认证服务（sso.php），以及使用redis代替session达到信息共享，而实现了单点服务。

具体流程如下：
	1.用户访问server1系统，server1系统是需要登录的，但用户现在没有登录。
	2.跳转到SSO登录系统，SSO系统也没有登录，弹出用户登录页。
	3.用户填写用户名、密码，SSO系统进行认证后，生成一个token，将token和对应的用户信息写入redis中，并且在浏览器（Browser）中写入SSO域下的Cookie，cookie的值就是刚刚生成的token。
	4.SSO跳转回到到server1系统，同时将token作为参数传递给server1系统。
	5.server1系统拿到token后，从后台向redis发送请求，验证token是否有效。
	6.验证通过后，server1系统设置server1域下的Cookie，将token的值写入cookie中。
至此，跨域单点登录就完成了。以后我们再访问server1系统时，server1就是登录的。接下来，我们再看看访问server2系统时的流程。

用户访问server2系统，server2系统没有登录，跳转到SSO。
	1.由于SSO已经登录了，不需要重新登录认证。
	2.SSO获取SSO域下cookie中的token值，通过浏览器跳转到server2系统，并将token作为参数传递给server2。
	3.server2得到token，使用token号访问redis，拿到用户信息。
	4.最后，server2也将token写入server2域下的Cookie。
这样，server2系统不需要走登录流程，就已经是登录了。SSO，server1和server2在不同的域也能实现单点登录了。

参考文章：
https://yq.aliyun.com/articles/636281 
