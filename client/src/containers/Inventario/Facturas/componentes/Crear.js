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
import { Checkbox,  Grid, InputAdornment, Tooltip } from '@material-ui/core';
import { editar as editarPedido, obtenerDetalleOrden, registrar as registrarPedido, obtenerInventarioOrden, guardarAlmacen } from '../../../../utils/API/pedidos';
import { obtenerTodos as obtenerRazones } from '../../../../utils/API/razones';
import { obtenerInventario, obtenerTodos as obtenerTodosBodegas } from '../../../../utils/API/bodegas';
import { obtenerTodos as obtenerProductos } from '../../../../utils/API/sistemas';
import IconButton from '@material-ui/core/IconButton';
import PersonOutlineIcon from '@material-ui/icons/PersonOutline';
import { Autocomplete } from '@material-ui/lab';
import MaterialTable from 'material-table';
import { LocalizationTable, TableIcons } from '../../../../utils/table';
import { registrarUnidad } from '../../../../utils/API/facturas';
import { obtenerTodos as obtenerClientes } from '../../../../utils/API/clientes';
import GroupAddIcon from '@material-ui/icons/GroupAdd';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Switch from '@material-ui/core/Switch';
import { Paper } from '@material-ui/core';
import { PersonAddOutlined, PostAddOutlined } from '@material-ui/icons';
import AccountBoxIcon from '@material-ui/icons/AccountBox';
import SupervisedUserCircleIcon from '@material-ui/icons/SupervisedUserCircle';
import CrearCliente from '../../Clientes/componentes/Crear'
const Transition = React.forwardRef(function Transition(props, ref) {
    return <Slide direction="up" ref={ref} {...props} />;
});
export default function CrearN(props) {
    const initializer = React.useContext(Initializer);
    const [open, setOpen] = React.useState(false)
    const [finalConsumer, setFinalConsumer] = React.useState(false)
    const [crearCliente, setCrearCliente] = React.useState(false)

    const [cantidad, setCantidad] = React.useState("")

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

    const [subTotalV, setSubTotalV] = React.useState(0)
    React.useEffect(() => {
        if (initializer.usuario != null) {

            obtenerTodosBodegas(setBodegaData, initializer)
            obtenerProductos(setProductosData, initializer)
            obtenerClientes(setClientData, initializer)
        }
    }, [initializer.usuario])
    React.useEffect(() => {
        if (props.sistema != null && props.open) {
            console.log(props.open)
            setProductos([])
            setAlmacen([])
            obtenerInventarioOrden(props.sistema.id, setProductos, initializer)


        }
    }, [props.sistema, props.open])
    const guardar = () => {
        if(finalConsumer==false&&client==""){
            initializer.mostrarNotificacion({ type: "warning", message: 'Seleccione un cliente' });
            return false
        }
        if(subTotalV==0){
            initializer.mostrarNotificacion({ type: "warning", message: 'No puede hacer una factura por valor 0' });
            return false
        }

            registrarUnidad({client_id:finalConsumer?'':client,final_consumer:finalConsumer?1:0,total:subTotalV+(subTotalV*0.12), data: productos }, initializer)

            props.setOpen(false)
            obtenerProductos(setProductosData, initializer)

            limpiar()
        
        
    }
    const limpiar = () => {

        setCantidad("")
        setSubTotalV(0)
        setClient("")
        setFinalConsumer(false)
        setProducto([])
        setProductos([])
        setAlmacen([])
        props.setSelected(null)
        props.setOpen(false)
        props.carga()
    }

    const getName = (id, data) => {
        let object = null
        data.map((e) => {
            if (id == e.id) {
                object = { ...e }
            }
        })
        return object
    }
    const validarData = () => {
        let final = []

        productos.map((e) => {

            if (!final.some(i => i.supplier_id === e.supplier_id)) {
                final.push({
                    supplier_id: e.supplier_id,
                    products: obtenerProductosPorId(e.supplier_id)
                })
            }

        })
        return final
    }
    const obtenerProductosPorId = (id) => {
        return productos.filter((e) => e.supplier_id == id)
    }
    const quitar = (row) => {
        console.log()
        let id = row.tableData.id
        let t = productos.slice()
        let tot = subTotalV - row.subtotal


        setProductos(t.filter((e, i) => i != id))
        setCantidad('')

        setProducto([])
        setSubTotalV(tot)
    }

    const existeEnDetalle=(id)=>{
        let exs = false
        productos.map((e)=>{
          if(e.product_id==id){
              exs=true
          }  
        })
        return exs
    }

    const obtenerBodega = (id) => {
        let nombre = ""
        bodegaData.map((e) => {
            if (e.id == id) {
                nombre = e.name
            }
        })
        return nombre
    }

    const quitarInventario = (row) => {

        let t = [];
        inventario.slice().map((e, i) => {
            if (!estaIncluido(e.inventory_id, row)) {
                t.push({ ...e })
            }
        })
        setInventario(t)

    }
    const estaIncluido = (id, dt) => {
        let res = false
        dt.map((e, i) => {
            if (e.inventory_id == id) {
                res = true
            }
        })
        return res
    }

    const cargarInventario = (id) => {
        if (id != '') {
            setInventario([])
            obtenerInventario(id, setInventario, initializer)
        }

    }
    const agregar = () => {
        if (producto.length != 0 != "" && cantidad != "" && cantidad > 0 && cantidad > 0) {
            let t = productos.slice()
            let subT = subTotalV
            let outStock=false
            producto.map((e) => {
                if(!existeEnDetalle(e.id)){
                    if((e.stock-cantidad)>=0){
                        t.push({ product: e.name, bar_code: e.bar_code, stock: (e.stock-cantidad), product_id: e.id, quantity: cantidad, price: e.sale_price, subtotal: (cantidad * e.sale_price) })
                        subT = subT + (cantidad * e.sale_price)
                    }else{
                        outStock=true
                    }
                  
                }
           

            })
            setSubTotalV(subT)
            setProductos(t)
            setCantidad('')

            setProducto([])
            if(outStock){
                initializer.mostrarNotificacion({ type: "warning", message: 'No hay suficiente stock' });
            }else{
                if(t.length==0){
                    initializer.mostrarNotificacion({ type: "warning", message: 'Producto ya agregado' });
         }
            }
           
        } else {
            initializer.mostrarNotificacion({ type: "warning", message: 'No deje campos vacíos' });

        }

    }

    const getName3 = (id) => {
        let object = null
        clientData.map((e) => {
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
        
  <CrearCliente sistema={null} setSelected={()=>null} setOpen={setCrearCliente} open={crearCliente} carga={()=>{
                  obtenerClientes(setClientData, initializer)

}} />

            <DialogTitle id="alert-dialog-slide-title" >
                
                <span>Factura</span> 
           
                </DialogTitle>
            <DialogContent >
            <DialogContentText id="alert-dialog-slide-description" style={{display:'flex',justifyContent:'space-between',alignItems:'center'}}>
                    Seleccione el cliente y productos a facturar
                    <div style={{display:'flex'}}>
             
                <Tooltip title="Crear cliente">
                <IconButton aria-label="add_proveedor" onClick={()=>setCrearCliente(true)}>
                    <PersonAddOutlined />
                </IconButton>
                </Tooltip>
                <Tooltip title={!finalConsumer?"Consumidor final":"Cliente"}>
                <IconButton aria-label="cambiar" onClick={()=>setFinalConsumer(!finalConsumer)}>
                   { finalConsumer?<AccountBoxIcon />:<SupervisedUserCircleIcon />}
                </IconButton>
                </Tooltip>
                
                </div>
             
                  
                </DialogContentText>
                <Grid container spacing={2}>
                      
                           { !finalConsumer&&(
                                <Grid item xs={12} md={4} style={{ display: 'flex',justifyContent:'space-between',alignItems:'center'}}>
                                <Autocomplete
                        
                                    style={{ width: '100%' }}
                                    size="small"
                                    options={clientData}
                                    
                                    value={getName3(client)}
                                    onChange={(event, newValue) => {
                                        if (newValue != null) {
        
                                            setClient(newValue.id);
                                        } else {
        
                                            setClient('')
        
                                        }
        
                                    }}
                                    getOptionLabel={(option) => option.document + " - " + option.names}
                                    // prints the selected value
                                    renderInput={params => (
                                        <TextField variant="outlined" {...params} label="Seleccione un cliente" variant="outlined" fullWidth  />
                                    )}
                                />
                             
                            </Grid>
                            )
                                    }
                                
                         
                   
                    <Grid item xs={12} md={finalConsumer?6:4} style={{ display: 'flex' }}>
                        <Autocomplete

                            style={{ width: '100%' }}
                            size="small"
                            options={productosData}
                            multiple
                            value={producto}
                            onChange={(event, newValue) => {
                                setProducto(newValue);
                            }}
                            getOptionLabel={(option) => option.bar_code + " - " + option.name+"- stock: "+option.stock}
                            // prints the selected value
                            renderInput={params => (
                                <TextField variant="outlined" {...params} label="Seleccione un producto" variant="outlined" fullWidth />
                            )}
                        />

                    </Grid>
                    <Grid item xs={12}  md={finalConsumer?6:4} >    <TextField
                        variant="outlined"
                        style={{ width: '100%' }}
                        type="numeric"
                        size="small"
                        label="Cantidad"
                    
                        value={cantidad}
                        onChange={(e) => setCantidad(e.target.value)}

                    /></Grid>
                


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
                                { title: "Precio", field: "price", type: "currency" },

                                { title: "Cantidad", field: "quantity" },
                                { title: "SubTotal", field: "subtotal", type: "currency" },




                            ]}
                            data={
                                productos
                            }
                            title="Productos"

                            localization={LocalizationTable}
                            actions={[{
                                icon: TableIcons.Add,
                                tooltip: 'Agregar',
                                isFreeAction: true,
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
                            components={{
                                Container: props => <Paper {...props} elevation={0}/>
                           }}

                            options={{
                                pageSize: 10,
                                paging: false,
                                search: false,

                                actionsColumnIndex: -1,
                                width: '100%',
                                maxBodyHeight: 200,
                                
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
                    <Grid item xs={12} md={12} style={{display:'flex',justifyContent:'flex-end'}} >
                        <TextField
                            variant="outlined"
                        
                            size="small"
                            label="SubTotal"
                            value={subTotalV}
                            InputProps={{
                                startAdornment: <InputAdornment position="start">$</InputAdornment>,
                              }}
                        />
                        
                    </Grid>
                    <Grid item xs={12} md={12} style={{display:'flex',justifyContent:'flex-end'}} >
                    <TextField
                            variant="outlined"
                       
                            size="small"
                            label="Iva"
                            value={(subTotalV * 0.12).toFixed(2)}
                            InputProps={{
                                startAdornment: <InputAdornment position="start">$</InputAdornment>,
                              }}
                        />
                         
                </Grid>
                    <Grid item xs={12} md={12} style={{display:'flex',justifyContent:'flex-end'}} >
                    <TextField
                            variant="outlined"
                        
                            size="small"
                            label="Total"
                            value={subTotalV + (subTotalV * 0.12)}
                            InputProps={{
                                startAdornment: <InputAdornment position="start">$</InputAdornment>,
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
                <Button color="primary" disabled={productos.length == 0} onClick={() => {
                    guardar()
                }}>
                    Guardar
                </Button>
            </DialogActions>
        </Dialog>
    )
}
