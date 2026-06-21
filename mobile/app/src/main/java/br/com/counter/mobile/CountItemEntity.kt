package br.com.counter.mobile

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "count_items")
data class CountItemEntity(
    @PrimaryKey val id: Int,
    val countId: Int,
    val productName: String,
    val sku: String?,
    val barcode: String?,
    val unit: String?,
    val systemQuantity: Double,
    val countedQuantity: Double?,
    val difference: Double,
    val syncStatus: String,
    val dirty: Boolean
)
