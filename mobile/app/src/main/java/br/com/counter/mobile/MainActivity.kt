package br.com.counter.mobile

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import br.com.counter.mobile.databinding.ActivityMainBinding

class MainActivity : AppCompatActivity() {
    private lateinit var binding: ActivityMainBinding
    private lateinit var viewModel: MainViewModel
    private val adapter = InventoryCountAdapter { count ->
        val intent = Intent(this, CountItemsActivity::class.java)
        intent.putExtra("count_id", count.id)
        intent.putExtra("count_title", count.title)
        intent.putExtra("count_status", count.status)
        startActivity(intent)
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        viewModel = ViewModelProvider(this)[MainViewModel::class.java]
        binding.recyclerCounts.layoutManager = LinearLayoutManager(this)
        binding.recyclerCounts.adapter = adapter
        binding.textUser.text = viewModel.userLabel()

        binding.buttonRefresh.setOnClickListener {
            viewModel.refresh()
        }

        binding.buttonRefreshBottom.setOnClickListener {
            viewModel.refresh()
        }

        binding.buttonLogout.setOnClickListener {
            viewModel.logout()
        }

        binding.buttonLogoutBottom.setOnClickListener {
            viewModel.logout()
        }

        observe()
        viewModel.load()
    }

    private fun observe() {
        viewModel.counts.observe(this) {
            adapter.update(it)
            binding.layoutEmptyCounts.visibility = if (it.isEmpty()) View.VISIBLE else View.GONE
        }

        viewModel.summary.observe(this) {
            binding.textSummary.text = if (it == null) {
                "Nenhum resumo carregado."
            } else {
                "Contagens abertas: ${it.openCounts}\nItens pendentes: ${it.pendingItems}\nItens sincronizados: ${it.syncedItems}\nItens contados: ${it.countedItems}"
            }
        }

        viewModel.message.observe(this) {
            if (!it.isNullOrBlank()) {
                UiFeedback.showToast(this, it)
            }
        }

        viewModel.loading.observe(this) {
            binding.layoutLoadingCounts.visibility = if (it) View.VISIBLE else View.GONE
            binding.recyclerCounts.visibility = if (it) View.INVISIBLE else View.VISIBLE
            if (it) {
                binding.layoutEmptyCounts.visibility = View.GONE
            }
            binding.buttonRefreshBottom.isEnabled = !it
            binding.buttonRefreshBottom.text = if (it) "Atualizando" else "Atualizar"
        }

        viewModel.loggedOut.observe(this) {
            if (it) {
                startActivity(Intent(this, LoginActivity::class.java))
                finish()
            }
        }
    }
}
