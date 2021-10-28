import React from 'react'
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';
import TextField from '@material-ui/core/TextField';
import Button from '@material-ui/core/Button';
import Initializer from '../../../../store/Initializer'
import Confirmar from '../../../../components/Confirmar'
import Slide from '@material-ui/core/Slide';
import { Checkbox, FormControlLabel, Grid } from '@material-ui/core';
import { editar as editarPedido, obtenerDetalleOrden, registrar as registrarPedido,obtenerInventarioOrden, guardarAlmacen } from '../../../../utils/API/pedidos';
import { obtenerTodos as obtenerRazones } from '../../../../utils/API/razones';
import { obtenerInventario, obtenerTodos as obtenerTodosBodegas } from '../../../../utils/API/bodegas';
import { obtenerTodos as obtenerProductos } from '../../../../utils/API/sistemas';

import { Autocomplete } from '@material-ui/lab';
import MaterialTable from 'material-table';
import { LocalizationTable, TableIcons } from '../../../../utils/table';
import { registrarUnidad } from '../../../../utils/API/ajustes';
const Transition = React.forwardRef(function Transition(props, ref) {
    return <Slide direction="up" ref={ref} {...props} />;
});
export default function Crear(props) {
    const initializer = React.useContext(Initializer);
    const [open, setOpen] = React.useState(false)

    const [cantidad, setCantidad] = React.useState("")
    const [razon, setRazon] = React.useState("")
    const [razonData, setRazonData] = React.useState([])
    const [inventario, setInventario] = React.useState([])
    const [productos, setProductos] = React.useState([])


    const [almacen, setAlmacen] = React.useState([])
    const [bodegaData, setBodegaData] = React.useState([])
    const [bodegaO, setBodegaO] = React.useState('')
    const [bodegaD, setBodegaD] = React.useState('')
    const [productosData, setProductosData] = React.useState([])
    const [producto, setProducto] = React.useState([])

    const [clientData, setClientData] = React.useState([])
    const [client, setClient] = React.useState("")
    React.useEffect(() => {
        if (initializer.usuario != null) {
            obtenerRazones(setRazonData, initializer)
            obtenerTodosBodegas(setBodegaData, initializer)
            obtenerProductos(setProductosData, initializer)

        }
    }, [initializer.usuario ])
    React.useEffect(() => {
        if (props.sistema != null&&props.open) {
            console.log(props.open)
            setProductos([])
            setAlmacen([])
            obtenerInventarioOrden(props.sistema.id,setProductos, initializer)


        }
    }, [props.sistema,props.open])
    const guardar = () => {
      
        registrarUnidad({data:productos},initializer)
    
        props.setOpen(false)
    
        limpiar()
    }
    const limpiar = () => {
      
        setCantidad("")
        setRazon("")
        setProducto([])
        setProductos([])
        setAlmacen([])
        props.setSelected(null)
        props.setOpen(false)
        props.carga()
    }
   
    const getName = (id,data) => {
        let object = null
        data.map((e) => {
            if (id == e.id) {
                object = { ...e }
            }
        })
        return object
    }
    const validarData=()=>{
        let final=[]

        productos.map((e)=>{
            
            if (!final.some(i => i.supplier_id === e.supplier_id)) {
                final.push({
                    supplier_id:e.supplier_id,
                    products:obtenerProductosPorId(e.supplier_id)
                })
            }
           
        })
        return final
    }
    const obtenerProductosPorId=(id)=>{
        return productos.filter((e)=>e.supplier_id==id)
    }
    const quitar=(row) => {
        let id = row.tableData.id
        let t = productos.slice()
      

        setProductos(t.filter((e,i) =>i!=id))
        setCantidad('')
        setRazon('')
        setProducto([])
    }


  
    const obtenerBodega=(id)=> {
        let nombre=""
        bodegaData.map((e)=>{
            if(e.id==id){
                nombre=e.name
            }
        })
        return nombre
    }
    const obtenerRazon=(id)=> {
        let nombre=""
        razonData.map((e)=>{
            if(e.id==id){
                nombre=e.name
            }
        })
        return nombre
    }
   
    const quitarInventario=(row) => {
    
       let t = [];
       inventario.slice().map((e,i) =>{
           if(!estaIncluido(e.inventory_id,row)){
            t.push({...e})
           }
       })
       setInventario(t)
       
    }
    const estaIncluido=(id,dt) => {
        let res = false
        dt.map((e,i) =>{
            if(e.inventory_id==id){
                res = true
            }
        })
        return res
    }
    
    const cargarInventario=(id)=>{
        if (id!=''){
            setInventario([])
            obtenerInventario(id,setInventario,initializer)
        }
  
    }
    const agregar=() => {
        if(producto.length!=0!=""&&razon!=""&&cantidad!=""&&cantidad>0&&cantidad>0){
            let t = productos.slice()
            producto.map((e)=>{
                t.push({product:e.name,bar_code:e.bar_code,stock:e.stock,product_id:e.id,reason_id:razon,reason:obtenerRazon(razon),quantity:cantidad})

            })
            setProductos(t)
            setCantidad('')
  
            setProducto([])
        }else{
            initializer.mostrarNotificacion({ type: "warning", message: 'No deje campos vacíos' });

        }
       
    }
    const getName2 = (id) => {
        let object = null
        razonData.map((e) => {
            if (id == e.id) {
                object = { ...e }
            }
        })
        return object
    }
    
    return (
        <Dialog
        fullWidth
        maxWidth='lg'
        fullScreen
            open={props.open}
            TransitionComponent={Transition}
            keepMounted
            onClose={() => {
                props.setOpen(false)
                limpiar()
            }}
            aria-labelledby="alert-dialog-slide-title"
            aria-describedby="alert-dialog-slide-description"
        >
            
            <DialogTitle id="alert-dialog-slide-title">Ajuste</DialogTitle>
            <DialogContent>
                <DialogContentText id="alert-dialog-slide-description">
                    Seleccione los productos y cantidades a ajustar
                </DialogContentText>
                <Grid container spacing={2}>
                <Grid item xs={12} md={12} style={{ display: 'flex' }}>
                        <Autocomplete

                            style={{ width: '100%' }}
                            size="small"
                            options={productosData}
                            multiple
                            value={producto}
                            onChange={(event, newValue) => {
                                setProducto(newValue);
                              }}
                            getOptionLabel={(option) => option.bar_code+" - "+option.name}
                         // prints the selected value
                            renderInput={params => (
                                <TextField    variant="outlined" {...params} label="Seleccione un producto" variant="outlined" fullWidth />
                            )}
                        />

                    </Grid>
                    <Grid item xs={12}>    <TextField
                        variant="outlined"
                        style={{  width: '100%' }}
                        type="number"
                        size="small"
                        label="Cantidad"
                        min="0"
                        value={cantidad}
                        onChange={(e) => setCantidad(e.target.value)}

                    /></Grid>
                  <Grid item xs={12} md={12} style={{ display: 'flex' }}>
                        <Autocomplete

                            style={{ width: '100%' }}
                            size="small"
                            options={razonData}
                            
                            value={getName2(razon)}
                            onChange={(event, newValue) => {
                              
                                if (newValue != null) {

                                    setRazon(newValue.id);
                                } else {

                                    setRazon('')

                                }
                              }}
                            getOptionLabel={(option) =>option.name}
                         // prints the selected value
                            renderInput={params => (
                                <TextField    variant="outlined" {...params} label="Seleccione un tipo" variant="outlined" fullWidth />
                            )}
                        />

                    </Grid>
                 
                 <Grid item xs={12} md={12}>

                 <MaterialTable
                 key={1}
                 id={1}
                    icons={TableIcons}
                    columns={[
                 

                        {
                            title: 'Producto',
                            field: 'product',
                            render: rowData => (
                             <span >{rowData.product}</span>
                            ),
                          },
                        { title: "Código de Barras", field: "bar_code" },
                        { title: "Stock", field: "stock" },

                        { title: "Cantidad", field: "quantity" },
                        { title: "Tipo", field: "reason" }

                



                    ]}
                    data={
                        productos
                    }
                    title="Productos a ajustar"
                   
                    localization={LocalizationTable}
                    actions={[      {
                        icon: TableIcons.Add,
                        tooltip: 'Agregar',
                        isFreeAction:true,
                        onClick: (event, rowData) => {
                          agregar()
                        }
                    },
                    {
                        icon: TableIcons.Delete,
                        tooltip: 'Eliminar',
                     
                        onClick: (event, rowData) => {
                          quitar(rowData)
                        }
                    }]}
                  

                    options={{
                        pageSize:10,
                        paging:false,
                        search:false,
                     
                        actionsColumnIndex: -1,
                        width:'100%',
                        maxBodyHeight: 150,
                        padding: 'dense',
                        headerStyle: {
                            textAlign: 'left'
                        },
                        cellStyle: {
                            textAlign: 'left'
                        },
                        searchFieldStyle: {

                            padding: 5
                        }
                    }}

                />
</Grid>


                </Grid>

            </DialogContent>
            <DialogActions>
                <Button onClick={() => {
                      limpiar()
                }} color="default">
                    Cancelar
                </Button>
                <Button color="primary" disabled={productos.length==0} onClick={()=>{
                  guardar()
                }}>
                    Guardar
                </Button>
            </DialogActions>
        </Dialog>
    )
}
