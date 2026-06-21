package br.com.counter.mobile

import com.google.gson.annotations.SerializedName

data class ApiResponse<T>(
    val success: Boolean,
    val data: T?,
    val message: String?,
    val errors: Map<String, List<String>>?
)

data class LoginRequest(
    val email: String,
    val password: String
)

data class LoginData(
    val token: String,
    val user: UserData
)

data class UserData(
    val id: Int,
    val name: String,
    val email: String,
    val role: String
)

data class SummaryData(
    @SerializedName("open_counts")
    val openCounts: Int,
    @SerializedName("pending_items")
    val pendingItems: Int,
    @SerializedName("synced_items")
    val syncedItems: Int,
    @SerializedName("counted_items")
    val countedItems: Int,
    @SerializedName("last_counted_at")
    val lastCountedAt: String?
)

data class InventoryCountData(
    val id: Int,
    val title: String,
    val status: String,
    @SerializedName("items_count")
    val itemsCount: Int,
    @SerializedName("started_at")
    val startedAt: String?,
    @SerializedName("finished_at")
    val finishedAt: String?,
    @SerializedName("approved_at")
    val approvedAt: String?
)

data class CountItemData(
    val id: Int,
    val product: ProductData?,
    @SerializedName("system_quantity")
    val systemQuantity: Double,
    @SerializedName("counted_quantity")
    val countedQuantity: Double?,
    val difference: Double,
    @SerializedName("sync_status")
    val syncStatus: String,
    @SerializedName("counted_at")
    val countedAt: String?
)

data class ProductData(
    val id: Int,
    val name: String,
    val sku: String?,
    val barcode: String?,
    val unit: String?
)

data class SyncRequest(
    val items: List<SyncItemRequest>
)

data class SyncItemRequest(
    val id: Int,
    @SerializedName("counted_quantity")
    val countedQuantity: Double?
)
