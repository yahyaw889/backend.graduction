<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Register — Simple Responsive Form</title>
  <style>
    :root{
      --bg:#0f1724;
      --card:#0b1220;
      --accent:#6ee7b7;
      --muted:#9aa4b2;
      --danger:#ff6b6b;
      --glass: rgba(255,255,255,0.03);
      --radius:14px;
      --max-width:920px;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      background: radial-gradient(1000px 600px at 10% 10%, rgba(110,231,183,0.06), transparent 8%),
                  linear-gradient(180deg,#071222 0%, var(--bg) 100%);
      color:#e6eef6;
      display:flex;align-items:center;justify-content:center;padding:32px;
    }

    .container{
      width:100%;max-width:var(--max-width);display:grid;grid-template-columns:1fr 420px;gap:28px;align-items:center;
    }

    .card{
      background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius:var(--radius);padding:28px;border:1px solid rgba(255,255,255,0.03);
      box-shadow:0 8px 40px rgba(2,6,23,0.6);
    }

    h2{margin-top:14px;margin-bottom:6px}
    .small{font-size:13px;color:var(--muted)}

    form{display:flex;flex-direction:column;gap:14px}
    label{font-size:13px;color:var(--muted);display:block;margin-bottom:6px}
    .field{display:flex;flex-direction:column}
    input[type=text],input[type=email],input[type=password]{
      width:100%;padding:12px 14px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);
      background:var(--glass);color:inherit;font-size:15px;outline:none;
      transition:box-shadow .15s, border-color .15s;
    }
    input::placeholder{color:rgba(230,238,246,0.45)}
    input:focus{box-shadow:0 6px 24px rgba(14,30,37,0.45);border-color:rgba(110,231,183,0.25)}

    .row{display:flex;gap:12px}
    .row .col{flex:1}
    .password-toggle{display:inline-flex;gap:8px;align-items:center;font-size:13px;color:var(--muted);cursor:pointer}
    .actions{display:flex;flex-direction:column;gap:12px;margin-top:6px}
    .btn{
      display:inline-flex;align-items:center;justify-content:center;padding:12px 14px;border-radius:10px;border:0;font-weight:600;cursor:pointer;font-size:15px;
      background:linear-gradient(90deg,var(--accent), #34d399);color:#042018;box-shadow:0 8px 24px rgba(52,211,153,0.12);
    }
    .btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)}

    /* social buttons */
    .social-buttons{
      display:flex;
      gap:12px;
      justify-content:space-between;
      margin-top:10px;
    }
    .social-btn{
      flex:1;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:10px;
      border-radius:10px;
      background:var(--glass);
      border:1px solid rgba(255,255,255,0.05);
      color:#e6eef6;
      cursor:pointer;
      font-weight:500;
      font-size:14px;
      transition:background .2s,border .2s;
    }
    .social-btn:hover{
      background:rgba(255,255,255,0.08);
      border-color:rgba(255,255,255,0.08);
    }
    .social-btn img{
      width:18px;
      height:18px;
    }

    .muted-row{display:flex;justify-content:space-between;align-items:center;color:var(--muted);font-size:13px}
    @media (max-width:980px){.container{grid-template-columns:1fr;}}
    @media (max-width:520px){body{padding:18px}}
  </style>
</head>
<body>
  <main class="container">
    <section class="card" aria-labelledby="register-title">
      <div>
        <div class="logo" aria-hidden>
          <div class="mark" style="width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,var(--accent), #34d399);display:flex;align-items:center;justify-content:center;color:#022;font-weight:700">RG</div>
        </div>
        <h2 id="register-title">Create an account</h2>
        <p class="small">Join us — create your account to get access to exclusive features and personalize your experience.</p>
      </div>

      <form id="regForm" action="{{ route('register.store') }}" method="POST">
        @csrf

        <div class="row">
          <div class="col field">
            <label for="username">Username</label>
            <input id="username" name="name" type="text" placeholder="Ahmed" required minlength="2" />
          </div>
        </div>

        <div class="field">
          <label for="email">Email address</label>
          <input id="email" name="email" type="email" placeholder="you@example.com" required />
        </div>

        <div class="row">
          <div class="col field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="At least 8 characters" required minlength="8" />
          </div>
          <div class="col field">
            <label for="confirm">Confirm password</label>
            <input id="confirm" name="password_confirmation" type="password" placeholder="Re-enter password" required minlength="8" />
          </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px">
          <label class="password-toggle"><input type="checkbox" id="showPwd" /> Show password</label>
          <div class="helper" style="font-size:13px;color:var(--muted)">Password must be at least 8 characters.</div>
        </div>

        <div class="actions">
          <button class="btn" type="submit">Create account</button>
        </div>

        <!-- social login buttons (no action) -->
        <div class="social-buttons">
          <a href="{{ route('google.redirect') }}" type="button" class="social-btn">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" alt="Google">
            Google
          </a>
          <a href="{{ route('facebook.redirect') }}" type="button" class="social-btn">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/facebook/facebook-original.svg" alt="Facebook">
            Facebook
          </a>
        </div>

        <div class="muted-row" style="margin-top:14px;">
          <div>Already have an account? <a href="#" style="color:var(--accent);text-decoration:none">Sign in</a></div>
        </div>
      </form>
    </section>
  </main>

  <script>
    // وظيفة show password فقط
    document.getElementById('showPwd').addEventListener('change', e=>{
      const type = e.target.checked ? 'text' : 'password';
      document.getElementById('password').type = type;
      document.getElementById('confirm').type = type;
    });
  </script>
</body>
</html>
