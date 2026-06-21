package br.com.counter.mobile

import android.content.Context

class InventoryRepository(context: Context) {
    private val sessionManager = SessionManager(context)
    private val apiService = ApiClient.create(context, sessionManager)
    private val dao = CounterDatabase.getDatabase(context).counterDao()

    suspend fun summary(): Result<SummaryData> {
        return try {
            val response = apiService.summary()
            val body = response.body()

            if (response.isSuccessful && body?.success == true && body.data != null) {
                Result.success(body.data)
            } else {
                Result.failure(Exception(body?.message ?: "Não foi possível carregar o resumo."))
            }
        } catch (exception: Exception) {
            Result.failure(Exception(connectionMessage()))
        }
    }

    suspend fun refreshCounts(): Result<List<InventoryCountEntity>> {
        return try {
            val response = apiService.counts()
            val body = response.body()

            if (response.isSuccessful && body?.success == true && body.data != null) {
                val counts = body.data.map {
                    InventoryCountEntity(
                        id = it.id,
                        title = it.title,
                        status = it.status,
                        itemsCount = it.itemsCount
                    )
                }
                dao.saveCounts(counts)
                Result.success(counts)
            } else {
                Result.failure(Exception(body?.message ?: "Não foi possível carregar as contagens."))
            }
        } catch (exception: Exception) {
            val localCounts = dao.getCounts()
            if (localCounts.isNotEmpty()) {
                Result.success(localCounts)
            } else {
                Result.failure(Exception(connectionMessage()))
            }
        }
    }

    suspend fun localCounts(): List<InventoryCountEntity> {
        return dao.getCounts()
    }

    suspend fun refreshItems(countId: Int): Result<List<CountItemEntity>> {
        return try {
            val response = apiService.countItems(countId)
            val body = response.body()

            if (response.isSuccessful && body?.success == true && body.data != null) {
                val items = body.data.map {
                    CountItemEntity(
                        id = it.id,
                        countId = countId,
                        productName = it.product?.name ?: "Produto não informado",
                        sku = it.product?.sku,
                        barcode = it.product?.barcode,
                        unit = it.product?.unit,
                        systemQuantity = it.systemQuantity,
                        countedQuantity = it.countedQuantity,
                        difference = it.difference,
                        syncStatus = it.syncStatus,
                        dirty = false
                    )
                }
                dao.saveItems(items)
                Result.success(items)
            } else {
                Result.failure(Exception(body?.message ?: "Não foi possível carregar os itens."))
            }
        } catch (exception: Exception) {
            val localItems = dao.getItems(countId)
            if (localItems.isNotEmpty()) {
                Result.success(localItems)
            } else {
                Result.failure(Exception(connectionMessage()))
            }
        }
    }

    suspend fun localItems(countId: Int): List<CountItemEntity> {
        return dao.getItems(countId)
    }

    suspend fun saveQuantity(itemId: Int, quantity: Double?) {
        dao.updateQuantity(itemId, quantity)
    }

    suspend fun syncItems(countId: Int): Result<Unit> {
        val dirtyItems = dao.dirtyItems(countId)

        if (dirtyItems.isEmpty()) {
            return Result.success(Unit)
        }

        return try {
            val request = SyncRequest(
                items = dirtyItems.map {
                    SyncItemRequest(
                        id = it.id,
                        countedQuantity = it.countedQuantity
                    )
                }
            )
            val response = apiService.syncItems(countId, request)
            val body = response.body()

            if (response.isSuccessful && body?.success == true) {
                dao.markSynced(countId)
                refreshItems(countId)
                Result.success(Unit)
            } else {
                Result.failure(Exception(body?.message ?: "Não foi possível sincronizar os itens."))
            }
        } catch (exception: Exception) {
            Result.failure(Exception(connectionMessage()))
        }
    }

    private fun connectionMessage(): String {
        return "Não foi possível conectar à API. Verifique a internet e se o servidor está ativo."
    }
}
