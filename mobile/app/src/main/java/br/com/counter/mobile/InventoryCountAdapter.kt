package br.com.counter.mobile

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import br.com.counter.mobile.databinding.RowInventoryCountBinding

class InventoryCountAdapter(
    private val onClick: (InventoryCountEntity) -> Unit
) : RecyclerView.Adapter<InventoryCountAdapter.ViewHolder>() {
    private val counts = mutableListOf<InventoryCountEntity>()

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = RowInventoryCountBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return counts.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(counts[position])
    }

    fun update(items: List<InventoryCountEntity>) {
        counts.clear()
        counts.addAll(items)
        notifyDataSetChanged()
    }

    inner class ViewHolder(private val binding: RowInventoryCountBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(count: InventoryCountEntity) {
            binding.textTitle.text = count.title
            binding.textStatus.text = statusLabel(count.status)
            binding.viewStatusDot.background = ContextCompat.getDrawable(binding.root.context, statusDot(count.status))
            binding.textItems.text = "${count.itemsCount} itens"
            binding.root.setOnClickListener {
                onClick(count)
            }
        }

        private fun statusLabel(status: String): String {
            return when (status) {
                "open" -> "Aberta"
                "in_progress" -> "Em andamento"
                "finished" -> "Finalizada"
                "approved" -> "Aprovada"
                else -> status
            }
        }

        private fun statusDot(status: String): Int {
            return when (status) {
                "open" -> R.drawable.bg_status_open
                "in_progress" -> R.drawable.bg_status_progress
                "finished" -> R.drawable.bg_status_finished
                "approved" -> R.drawable.bg_status_approved
                else -> R.drawable.bg_status_finished
            }
        }
    }
}
