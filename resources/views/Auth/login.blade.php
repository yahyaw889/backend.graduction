<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login — Simple Responsive Form</title>
  <style>
    :root{
      --bg:#0f1724;
      --card:#0b1220;
      --accent:#6ee7b7;
      --muted:#9aa4b2;
      --danger:#ff6b6b;
      --glass:rgba(255,255,255,0.03);
      --radius:14px;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      display:flex;align-items:center;justify-content:center;
      background: radial-gradient(1000px 600px at 10% 10%, rgba(110,231,183,0.06), transparent 8%), linear-gradient(180deg,#071222 0%, var(--bg) 100%);
      color:#e6eef6;
      padding:32px;
    }

    .card{
      width:100%;max-width:420px;padding:32px;border-radius:var(--radius);
      background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border:1px solid rgba(255,255,255,0.04);
      box-shadow:0 8px 40px rgba(2,6,23,0.6);
    }

    h1{margin-top:0;margin-bottom:8px;font-size:26px}
    p.small{margin:0 0 20px;color:var(--muted);}

    form{display:flex;flex-direction:column;gap:14px}
    label{font-size:13px;color:var(--muted);margin-bottom:4px}
    input[type=email],input[type=password]{
      width:100%;padding:12px 14px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);
      background:var(--glass);color:inherit;font-size:15px;outline:none;transition:box-shadow .15s,border-color .15s;
    }
    input:focus{border-color:rgba(110,231,183,0.25);box-shadow:0 4px 20px rgba(14,30,37,0.45)}
    input::placeholder{color:rgba(230,238,246,0.45)}

    .actions{display:flex;flex-direction:column;gap:12px;margin-top:8px}
    .btn{
      display:inline-flex;align-items:center;justify-content:center;gap:8px;
      padding:12px 14px;border-radius:10px;border:0;font-weight:600;cursor:pointer;
      font-size:15px;background:linear-gradient(90deg,var(--accent), #34d399);
      color:#042018;box-shadow:0 8px 24px rgba(52,211,153,0.12);
    }
    .btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)}

    .btn.google{
      background:#fff;
      color:#222;
      flex:1;
    }
    .btn.facebook{
      background:#1877f2;
      color:#fff;
      flex:1;
    }

    .social-buttons{
      display:flex;
      gap:10px;
      margin-top:12px;
    }

    .social-buttons img{
      width:20px;
      height:20px;
    }

    .muted-row{display:flex;justify-content:space-between;align-items:center;color:var(--muted);font-size:13px}
    .error{color:var(--danger);font-size:13px;margin-top:4px}

    .password-toggle{display:inline-flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);cursor:pointer}

    @media(max-width:520px){
      body{padding:18px}
      .card{padding:24px}
      .social-buttons{flex-direction:column;}
    }
  </style>
</head>
<body>
  <section class="card">
    <div class="logo" style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
      <div style="width:46px;height:46px;border-radius:10px;background:linear-gradient(135deg,var(--accent),#34d399);display:flex;align-items:center;justify-content:center;color:#022;font-weight:700">LG</div>
      <h1>Welcome back</h1>
    </div>
    <p class="small">Login to access your account and continue where you left off.</p>

    <form id="loginForm" novalidate action="{{ route('login.authenticate') }}" method="POST">
      @csrf
      <div class="field">
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" placeholder="you@example.com" required />
        <div class="error" id="err-email"></div>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="Your password" required minlength="8" />
        <div class="error" id="err-password"></div>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-top:2px">
        <label class="password-toggle"><input type="checkbox" id="showPwd" /> Show password</label>
        <a href="#" style="color:var(--accent);font-size:13px;text-decoration:none">Forgot password?</a>
      </div>

      <div class="actions">
        <button class="btn" type="submit">Login</button>
      </div>

      <div class="social-buttons">
        <a href="{{ route('google.redirect') }}" type="button" class="btn google">
          <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo" />
          Google
        </a>
        <a href="{{ route('facebook.redirect') }}" type="button" class="btn facebook">
          <img src="https://www.svgrepo.com/show/452196/facebook-1.svg" alt="Facebook Logo" />
          Facebook
        </a>
      </div>

      <div class="muted-row" style="margin-top:8px">
        <div>Don’t have an account? <a href="#" style="color:var(--accent);text-decoration:none">Register</a></div>
      </div>
    </form>
  </section>

  <script>
    const form = document.getElementById('loginForm');
    const showPwd = document.getElementById('showPwd');

    showPwd.addEventListener('change', e => {
      const type = e.target.checked ? 'text' : 'password';
      document.getElementById('password').type = type;
    });

    document.getElementById('demoBtn').addEventListener('click', () => {
      document.getElementById('email').value = 'ahmed@example.com';
      document.getElementById('password').value = 'Pa$$w0rd123';
      clearErrors();
    });

    function clearErrors(){
      document.querySelectorAll('.error').forEach(el=>el.textContent='');
    }

    function showError(id,msg){
      document.getElementById(id).textContent = msg;
    }

    function validateEmail(email){
      return /^\S+@\S+\.\S+$/.test(email);
    }

    form.addEventListener('submit', e => {
      e.preventDefault();
      clearErrors();
      const email = document.getElementById('email').value.trim();
      const pwd = document.getElementById('password').value;
      let valid = true;
      if(!validateEmail(email)){
        showError('err-email','Enter a valid email address.');
        valid=false;
      }
      if(pwd.length < 8){
        showError('err-password','Password must be at least 8 characters.');
        valid=false;
      }
      if(!valid)return;
      alert('Logged in (demo).');
      form.reset();
    });
  </script>
</body>
</html>
