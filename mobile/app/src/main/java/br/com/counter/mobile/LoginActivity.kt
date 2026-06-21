package br.com.counter.mobile

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import br.com.counter.mobile.databinding.ActivityLoginBinding

class LoginActivity : AppCompatActivity() {
    private lateinit var binding: ActivityLoginBinding
    private lateinit var viewModel: LoginViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLoginBinding.inflate(layoutInflater)
        setContentView(binding.root)

        viewModel = ViewModelProvider(this)[LoginViewModel::class.java]

        binding.buttonLogin.setOnClickListener {
            viewModel.login(
                binding.editEmail.text.toString(),
                binding.editPassword.text.toString()
            )
        }

        observe()
    }

    private fun observe() {
        viewModel.loading.observe(this) {
            binding.buttonLogin.isEnabled = !it
            binding.buttonLogin.text = if (it) "Entrando" else "Entrar"
        }

        viewModel.error.observe(this) {
            binding.textError.visibility = if (it.isNullOrBlank()) View.GONE else View.VISIBLE
            binding.textError.text = it
            if (!it.isNullOrBlank()) {
                UiFeedback.showToast(this, it)
            }
        }

        viewModel.authenticated.observe(this) {
            if (it) {
                startActivity(Intent(this, MainActivity::class.java))
                finish()
            }
        }
    }
}
